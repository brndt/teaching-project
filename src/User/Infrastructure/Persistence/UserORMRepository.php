<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Email;
use LaSalle\StudentTeacher\User\Domain\User;
use LaSalle\StudentTeacher\User\Domain\UserRepository;
use LaSalle\StudentTeacher\User\Infrastructure\Persistence\Doctrine\UserDoctrineRepository;

final class UserORMRepository implements UserRepository
{
    private EntityManagerInterface $entityManager;
    private UserDoctrineRepository $doctrineRepository;

    public function __construct(EntityManagerInterface $entityManager, UserDoctrineRepository $doctrineRepository)
    {
        $this->entityManager = $entityManager;
        $this->doctrineRepository = $doctrineRepository;
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function searchByEmail(Email $email): ?User
    {
        return $this->doctrineRepository->findOneBy(['email' => $email]);
    }

    public function searchById(Uuid $id): ?User
    {
        return $this->doctrineRepository->findOneBy(['id' => $id]);
    }
}