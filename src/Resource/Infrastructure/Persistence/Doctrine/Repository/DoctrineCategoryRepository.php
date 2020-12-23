<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Category;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Shared\Infrastructure\Persistence\Doctrine\DoctrineCriteriaConverter;
use LaSalle\StudentTeacher\User\Domain\Aggregate\UserConnection;

final class DoctrineCategoryRepository implements CategoryRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(Category $category): void
    {
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    /**
     * @return object|Category|null
     */
    public function ofId(Uuid $id): ?Category
    {
        return $this->entityManager->getRepository(Category::class)->find($id);
    }

    public function nextIdentity(): Uuid
    {
        return Uuid::generate();
    }

    public function matching(Criteria $criteria): array
    {
        $doctrineCriteria = DoctrineCriteriaConverter::convert($criteria);
        return $this->entityManager->getRepository(Category::class)->matching(
            $doctrineCriteria
        )->toArray();
    }

    /**
     * @return object|Category|null
     */
    public function ofName(string $name): ?Category
    {
        return $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $name]);
    }
}