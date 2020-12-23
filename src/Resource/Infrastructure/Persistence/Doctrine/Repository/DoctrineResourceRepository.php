<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Resource;
use LaSalle\StudentTeacher\Resource\Domain\Repository\ResourceRepository;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Shared\Infrastructure\Persistence\Doctrine\DoctrineCriteriaConverter;

final class DoctrineResourceRepository implements ResourceRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(Resource $resource): void
    {
        $this->entityManager->persist($resource);
        $this->entityManager->flush();
    }

    /**
     * @return object|Resource|null
     */
    public function ofId(Uuid $id): ?Resource
    {
        return $this->entityManager->getRepository(Resource::class)->find($id);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::generate();
    }

    public function matching(Criteria $criteria): array
    {
        $doctrineCriteria = DoctrineCriteriaConverter::convert($criteria);
        return $this->entityManager->getRepository(Resource::class)->matching(
            $doctrineCriteria
        )->toArray();
    }

    /**
     * @return object|Resource|null
     */
    public function ofName(string $name): ?Resource
    {
        return $this->entityManager->getRepository(Resource::class)->findOneBy(['name' => $name]);
    }
}
