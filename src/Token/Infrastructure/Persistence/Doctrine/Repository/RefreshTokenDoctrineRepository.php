<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use LaSalle\StudentTeacher\Token\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\RefreshTokenString;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;

final class RefreshTokenDoctrineRepository implements RefreshTokenRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return object|User|null
     */
    public function ofRefreshTokenString(RefreshTokenString $token): ?RefreshToken
    {
        return $this->entityManager->getRepository(RefreshToken::class)->findOneBy(['refreshToken' => $token]);
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