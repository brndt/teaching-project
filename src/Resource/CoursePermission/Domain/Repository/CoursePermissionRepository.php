<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Repository;

use LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Aggregate\CoursePermission;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

interface CoursePermissionRepository
{
    public function save(CoursePermission $coursePermission): void;

    public function ofId(Uuid $id): ?CoursePermission;

    public function nextIdentity(): Uuid;

    public function matching(Criteria $criteria): array;
}
