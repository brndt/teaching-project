<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Unit\Application\Service;

use LaSalle\StudentTeacher\Resource\Course\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Course\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Resource\Unit\Application\Request\AuthorizedSearchUnitsByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Unit\Application\Response\UnitCollectionResponse;
use LaSalle\StudentTeacher\Resource\Unit\Application\Response\UnitResponse;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Aggregate\Unit;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Service\UnitService;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class AuthorizedSearchUnitsByCriteriaService
{
    private CourseService $courseService;
    private UserService $userService;
    private UnitService $unitService;

    public function __construct(
        private CourseRepository $courseRepository,
        private UnitRepository $unitRepository,
        UserRepository $userRepository,
        private AuthorizationService $authorizationService
    ) {
        $this->unitService = new UnitService($unitRepository);
        $this->courseService = new CourseService($courseRepository);
        $this->userService = new UserService($userRepository);
    }

    public function __invoke(AuthorizedSearchUnitsByCriteriaRequest $request): UnitCollectionResponse
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $courseId = new Uuid($request->getCourseId());
        $course = $this->courseService->findCourse($courseId);
        $this->authorizationService->ensureUserHasPermissionsToManageCourse($requestAuthor, $course);

        $filters = Filters::fromValues([['field' => 'courseId', 'operator' => '=', 'value' => $courseId->toString()]]);

        $criteria = new Criteria(
            $filters,
            Order::fromValues($request->getOrderBy(), $request->getOrder()),
            Operator::fromValue($request->getOperator()),
            $request->getOffset(),
            $request->getLimit()
        );

        $courses = $this->unitRepository->matching($criteria);

        return new UnitCollectionResponse(...$this->buildResponse(...$courses));
    }

    private function buildResponse(Unit ...$units): array
    {
        return array_map(
            static function (Unit $unit) {
                return new UnitResponse(
                    $unit->getId()->toString(),
                    $unit->getCourseId()->toString(),
                    $unit->getName(),
                    $unit->getDescription(),
                    $unit->getLevel(),
                    $unit->getCreated(),
                    $unit->getModified(),
                    $unit->getStatus()->value(),
                );
            },
            $units
        );
    }
}
