<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\UnauthorizedSearchUnitsByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Response\UnitCollectionResponse;
use LaSalle\StudentTeacher\Resource\Application\Response\UnitResponse;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Unit;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class UnauthorizedSearchUnitsByCriteriaService
{
    private UserService $userService;
    private CourseService $courseService;

    public function __construct(
        private UnitRepository $unitRepository,
        UserRepository $userRepository,
        CourseRepository $courseRepository
    ) {
        $this->userService = new UserService($userRepository);
        $this->courseService = new CourseService($courseRepository);
    }

    public function __invoke(UnauthorizedSearchUnitsByCriteriaRequest $request): UnitCollectionResponse
    {
        $criteria = new Criteria(
            Filters::fromValues($request->getFilters()),
            Order::fromValues($request->getOrderBy(), $request->getOrder()),
            Operator::fromValue($request->getOperator()),
            $request->getOffset(),
            $request->getLimit()
        );
        $units = $this->unitRepository->matching($criteria);
        return new UnitCollectionResponse(...$this->buildResponse(...$units));
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
