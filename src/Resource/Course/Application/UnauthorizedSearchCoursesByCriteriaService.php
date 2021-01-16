<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Course\Application;

use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;

final class UnauthorizedSearchCoursesByCriteriaService
{
    public function __construct(private CourseRepository $courseRepository)
    {
    }

    public function __invoke(UnauthorizedSearchCoursesByCriteriaRequest $request): CourseCollectionResponse
    {
        $criteria = new Criteria(
            Filters::fromValues($request->getFilters()),
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
