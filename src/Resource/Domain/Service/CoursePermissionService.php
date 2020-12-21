<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Service;

use LaSalle\StudentTeacher\Resource\Domain\Aggregate\CoursePermission;
use LaSalle\StudentTeacher\Resource\Domain\Exception\CoursePermissionAlreadyExists;
use LaSalle\StudentTeacher\Resource\Domain\Exception\CoursePermissionNotFound;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class CoursePermissionService
{
    private CoursePermissionRepository $repository;

    public function __construct(CoursePermissionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findCoursePermission(Uuid $courseId, Uuid $studentId): CoursePermission
    {
        $criteria = new Criteria(
            Filters::fromValues(
                [
                    ['field' => 'courseId', 'operator' => '=', 'value' => $courseId->toString()],
                    ['field' => 'studentId', 'operator' => '=', 'value' => $studentId->toString()]
                ]
            ), Order::fromValues(null, null), Operator::fromValue(null), null, null
        );
        $coursePermission = $this->repository->matching($criteria);

        if (true === empty($coursePermission)) {
            throw new CoursePermissionNotFound();
        }
        return $coursePermission[0];
    }

    public function ensureCoursePermissionNotExists(Uuid $courseId, Uuid $studentId): void
    {
        $criteria = new Criteria(
            Filters::fromValues(
                [
                    ['field' => 'courseId', 'operator' => '=', 'value' => $courseId->toString()],
                    ['field' => 'studentId', 'operator' => '=', 'value' => $studentId->toString()]
                ]
            ), Order::fromValues(null, null), Operator::fromValue(null), null, null
        );
        $coursePermission = $this->repository->matching($criteria);
        if (false === empty($coursePermission)) {
            throw new CoursePermissionAlreadyExists();
        }
    }
}
