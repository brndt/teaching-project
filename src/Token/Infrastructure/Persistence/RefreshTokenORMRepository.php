<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Infrastructure\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use LaSalle\StudentTeacher\Token\Domain\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\RefreshTokenRepository;
use LaSalle\StudentTeacher\Token\Infrastructure\Persistence\Doctrine\RefreshTokenDoctrineRepository;

final class RefreshTokenORMRepository implements RefreshTokenRepository
{
    private EntityManagerInterface $entityManager;
    private RefreshTokenDoctrineRepository $doctrineRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        RefreshTokenDoctrineRepository $doctrineRepository
    ) {
        $this->entityManager = $entityManager;
        $this->doctrineRepository = $doctrineRepository;
    }

    public function searchByTokenValue(string $token): ?RefreshToken
    {
        return $this->doctrineRepository->findOneBy(['refreshToken' => $token]);
    }

    public function delete(RefreshToken $token): void
    {
        $this->entityManager->remove($token);
        $this->entityManager->flush();
    }

    public function save(RefreshToken $token): void
    {
        $this->entityManager->persist($token);
        $this->entityManager->flush();
    }
}