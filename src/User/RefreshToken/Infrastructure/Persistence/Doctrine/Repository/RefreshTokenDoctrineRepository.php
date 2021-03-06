<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\RefreshToken\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use LaSalle\StudentTeacher\User\RefreshToken\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\User\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\RefreshToken\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Token;

final class RefreshTokenDoctrineRepository implements RefreshTokenRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @return object|User|null
     */
    public function ofToken(Token $token): ?RefreshToken
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