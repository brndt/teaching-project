<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use LaSalle\StudentTeacher\User\Domain\User;
use LaSalle\StudentTeacher\User\Domain\UserRepository;
use LaSalle\StudentTeacher\User\Infrastructure\Persistence\Doctrine\UserDoctrineRepository;

final class UserORMRepository implements UserRepository
{
    private EntityManagerInterface $entityManager;
    private UserDoctrineRepository $doctrineRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserDoctrineRepository $doctrineRepository
    ) {
        $this->entityManager = $entityManager;
        $this->doctrineRepository = $doctrineRepository;
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function updateBasicInformation(User $user): void
    {
        $userUpdated = $this->doctrineRepository->findOneBy(['id' => $user->getId()]);

        $userUpdated->setEmail($user->getEmail());
        $userUpdated->setFirstName($user->getFirstName());
        $userUpdated->setLastName($user->getLastName());
        $userUpdated->setEducation($user->getEducation());
        $userUpdated->setExperience($user->getExperience());
        $userUpdated->setImage($user->getImage());

        $this->entityManager->persist($userUpdated);
        $this->entityManager->flush();
    }

    public function searchByEmail(string $email): ?User
    {
        return $this->doctrineRepository->findOneBy(
            [
                'email' => $email,
            ]
        );
    }

    public function searchByUuid(string $uuid): ?User
    {
        return $this->doctrineRepository->findOneBy(
            [
                'uuid' => $uuid,
            ]
        );
    }

    public function searchById(int $id): ?User
    {
        return $this->doctrineRepository->findOneBy(
            [
                'id' => $id,
            ]
        );
    }
}