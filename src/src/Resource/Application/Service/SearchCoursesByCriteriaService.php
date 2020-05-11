<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\SearchCoursesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Response\CourseCollectionResponse;
use LaSalle\StudentTeacher\Resource\Application\Response\CourseResponse;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;

final class SearchCoursesByCriteriaService extends CourseService
{
    public function __invoke(SearchCoursesByCriteriaRequest $request): CourseCollectionResponse
    {
        $criteria = new Criteria(
            Filters::fromValues($request->getFilters()),
            Order::fromValues($request->getOrderBy(), $request->getOrder()),
            Operator::fromValue($request->getOperator()),
            $request->getOffset(),
            $request->getLimit()
        );

        $courses = $this->courseRepository->matching($criteria);

        $this->ensureCoursesExist($courses);

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