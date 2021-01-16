<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\CoursePermission\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use LaSalle\StudentTeacher\Resource\Course\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Aggregate\CoursePermission;
use LaSalle\StudentTeacher\Resource\CoursePermission\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Shared\Infrastructure\Persistence\Doctrine\DoctrineCriteriaConverter;

final class DoctrineCoursePermissionRepository implements CoursePermissionRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(CoursePermission $course): void
    {
        $this->entityManager->persist($course);
        $this->entityManager->flush();
    }

    /**
     * @return object|Course|null
     */
    public function ofId(Uuid $id): ?CoursePermission
    {
        return $this->entityManager->getRepository(CoursePermission::class)->find($id);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::generate();
    }

    public function matching(Criteria $criteria): array
    {
        $doctrineCriteria = DoctrineCriteriaConverter::convert($criteria);
        return $this->entityManager->getRepository(CoursePermission::class)->matching(
            $doctrineCriteria
        )->toArray();
    }
}
