<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Shared\Infrastructure\Persistence\Doctrine\DoctrineCriteriaConverter;
use LaSalle\StudentTeacher\User\Domain\Aggregate\UserConnection;
use LaSalle\StudentTeacher\User\Domain\Repository\UserConnectionRepository;

final class UserConnectionRepositoryDoctrineRepository implements UserConnectionRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(UserConnection $userConnection): void
    {
        $this->entityManager->persist($userConnection);
        $this->entityManager->flush();
    }

    /**
     * @return object|UserConnection|null
     */
    public function ofId(Uuid $studentId, Uuid $teacherId): ?UserConnection
    {
        return $this->entityManager->getRepository(UserConnection::class)->findOneBy(
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
        return $this->entityManager->getRepository(UserConnection::class)->matching(
            $doctrineCriteria
        )->toArray();
    }
}