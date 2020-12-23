<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchCoursesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Response\CourseCollectionResponse;
use LaSalle\StudentTeacher\Resource\Application\Response\CourseResponse;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class AuthorizedSearchCoursesByCriteriaService
{
    private CourseService $courseService;
    private UserService $userService;

    public function __construct(private CourseRepository $courseRepository, UserRepository $userRepository)
    {
        $this->courseService = new CourseService($courseRepository);
        $this->userService = new UserService($userRepository);
    }

    public function __invoke(AuthorizedSearchCoursesByCriteriaRequest $request): CourseCollectionResponse
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $filters = $this->courseService->createFiltersDependingByRoles($requestAuthor);

        if (null !== $request->getTeacherId()) {
            $teacherId = new Uuid($request->getTeacherId());
            $filters = $filters->add($this->courseService->createFilterByTeacherId($teacherId));
        }

        $criteria = new Criteria(
            $filters,
            Order::fromValues($request->getOrderBy(), $request->getOrder()),
            Operator::fromValue($request->getOperator()),
            $request->getOffset(),
            $request->getLimit()
        );

        $courses = $this->courseRepository->matching($criteria);

        return new CourseCollectionResponse(...$this->buildResponse(...$courses));
    }

    private function buildResponse(Course ...$courses): array
    {
        return array_map(
            static function (Course $course) {
                return new CourseResponse(
                    $course->getId()->toString(),
                    $course->getTeacherId()->toString(),
                    $course->getCategoryId()->toString(),
                    $course->getName(),
                    $course->getDescription(),
                    $course->getLevel(),
                    $course->getCreated(),
                    $course->getModified(),
                    $course->getStatus()->value(),
                );
            },
            $courses
        );
    }
}
