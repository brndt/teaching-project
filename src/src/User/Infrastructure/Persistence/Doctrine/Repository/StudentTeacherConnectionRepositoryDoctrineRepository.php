<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Shared\Infrastructure\Persistence\Doctrine\DoctrineCriteriaConverter;
use LaSalle\StudentTeacher\User\Domain\Aggregate\StudentTeacherConnection;
use LaSalle\StudentTeacher\User\Domain\Repository\StudentTeacherConnectionRepository;

final class StudentTeacherConnectionRepositoryDoctrineRepository implements StudentTeacherConnectionRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(StudentTeacherConnection $studentTeacherConnection): void
    {
        $this->entityManager->persist($studentTeacherConnection);
        $this->entityManager->flush();
    }

    /**
     * @return object|StudentTeacherConnection|null
     */
    public function ofId(Uuid $studentId, Uuid $teacherId): ?StudentTeacherConnection
    {
        return $this->entityManager->getRepository(StudentTeacherConnection::class)->findOneBy(
            ['studentId' => $studentId, 'teacherId' => $teacherId]
        );
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::generate();
    }

    public function matching(Criteria $criteria): array
    {
        $doctrineCriteria = DoctrineCriteriaConverter::convert($criteria);
        return $this->entityManager->getRepository(StudentTeacherConnection::class)->matching(
            $doctrineCriteria
        )->toArray();
    }
}