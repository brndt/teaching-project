<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Shared\Infrastructure\Persistence\Doctrine\DoctrineCriteriaConverter;

final class DoctrineCourseRepository implements CourseRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(Course $course): void
    {
        $this->entityManager->persist($course);
        $this->entityManager->flush();
    }

    /**
     * @return object|Course|null
     */
    public function ofId(Uuid $id): ?Course
    {
        return $this->entityManager->getRepository(Course::class)->find($id);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::generate();
    }

    public function matching(Criteria $criteria): array
    {
        $doctrineCriteria = DoctrineCriteriaConverter::convert($criteria);
        return $this->entityManager->getRepository(Course::class)->matching(
            $doctrineCriteria
        )->toArray();
    }
}