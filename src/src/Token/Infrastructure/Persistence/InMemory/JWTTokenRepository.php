<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Infrastructure\Persistence\InMemory;

use LaSalle\StudentTeacher\Token\Domain\Aggregate\Token;
use LaSalle\StudentTeacher\Token\Domain\Repository\TokenRepository;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\Entity\SymfonyUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

final class JWTTokenRepository implements TokenRepository
{
    private JWTTokenManagerInterface $tokenManager;

    public function __construct(JWTTokenManagerInterface $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    public function create(User $user): Token
    {
        $symfonyUser = new SymfonyUser(
            $user->getId()->toString(),
            $user->getEmail()->toString(),
            $user->getPassword()->toString(),
            SymfonyUser::processValueToSymfonyRole($user->getRoles()->toArrayOfPrimitives())
        );

        $tokenValue = $this->tokenManager->create($symfonyUser);
        return new Token($tokenValue);
    }
}