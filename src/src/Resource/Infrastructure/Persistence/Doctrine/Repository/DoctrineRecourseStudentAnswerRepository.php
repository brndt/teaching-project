<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\RecourseStudentAnswer;
use LaSalle\StudentTeacher\Resource\Domain\Repository\RecourseStudentAnswerRepository;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Shared\Infrastructure\Persistence\Doctrine\DoctrineCriteriaConverter;

final class DoctrineRecourseStudentAnswerRepository implements RecourseStudentAnswerRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(RecourseStudentAnswer $recourseStudentAnswer): void
    {
        $this->entityManager->persist($recourseStudentAnswer);
        $this->entityManager->flush();
    }

    /**
     * @return object|RecourseStudentAnswer|null
     */
    public function ofId(Uuid $id): ?RecourseStudentAnswer
    {
        return $this->entityManager->getRepository(RecourseStudentAnswer::class)->find($id);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::generate();
    }

    public function matching(Criteria $criteria): array
    {
        $doctrineCriteria = DoctrineCriteriaConverter::convert($criteria);
        return $this->entityManager->getRepository(RecourseStudentAnswer::class)->matching(
            $doctrineCriteria
        )->toArray();
    }
}
