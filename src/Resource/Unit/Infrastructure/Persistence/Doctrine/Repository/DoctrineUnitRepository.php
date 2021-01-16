<?php


namespace LaSalle\StudentTeacher\Resource\Unit\Infrastructure\Persistence\Doctrine\Repository;


use Doctrine\ORM\EntityManagerInterface;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Aggregate\Unit;
use LaSalle\StudentTeacher\Resource\Unit\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Shared\Infrastructure\Persistence\Doctrine\DoctrineCriteriaConverter;

class DoctrineUnitRepository implements UnitRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(Unit $unit): void
    {
        $this->entityManager->persist($unit);
        $this->entityManager->flush();
    }

    /**
     * @return object|Unit|null
     */
    public function ofId(Uuid $id): ?Unit
    {
        return $this->entityManager->getRepository(Unit::class)->find($id);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::generate();
    }

    public function matching(Criteria $criteria): array
    {
        $doctrineCriteria = DoctrineCriteriaConverter::convert($criteria);
        return $this->entityManager->getRepository(Unit::class)->matching(
            $doctrineCriteria
        )->toArray();
    }

    /**
     * @return object|Unit|null
     */
    public function ofName(string $name): ?Unit
    {
        return $this->entityManager->getRepository(Unit::class)->findOneBy(['name' => $name]);
    }

}