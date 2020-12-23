<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Persistence\InMemory;

use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\TokenManager;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\Entity\SymfonyUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

final class JWTTokenManager implements TokenManager
{
    public function __construct(private JWTTokenManagerInterface $tokenManager)
    {
    }

    public function generate(User $user): string
    {
        $symfonyUser = new SymfonyUser(
            $user->getId()->toString(),
            $user->getEmail()->toString(),
            $user->getPassword()->toString(),
            SymfonyUser::processValueToSymfonyRole($user->getRoles()->getArrayOfPrimitives()),
            $user->getEnabled()
        );

        return $this->tokenManager->create($symfonyUser);
    }
}