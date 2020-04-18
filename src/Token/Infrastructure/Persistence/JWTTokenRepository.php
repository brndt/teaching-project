<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Infrastructure\Persistence;

use LaSalle\StudentTeacher\Token\Domain\Token;
use LaSalle\StudentTeacher\Token\Domain\TokenRepository;
use LaSalle\StudentTeacher\User\Domain\User;
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
        $symfonyUser = new SymfonyUser($user->getUuid(), $user->getEmail(), $user->getPassword(), $user->getFirstName(), $user->getLastName(), $user->getRoles(), $user->getCreated());
        $tokenValue = $this->tokenManager->create($symfonyUser);
        return new Token($tokenValue);
    }
}