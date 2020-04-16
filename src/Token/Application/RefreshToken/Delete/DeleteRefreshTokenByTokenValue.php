<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\RefreshToken\Delete;

use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Domain\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\RefreshTokenRepository;

final class DeleteRefreshTokenByTokenValue
{
    private RefreshTokenRepository $repository;

    public function __construct(RefreshTokenRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(string $token): void
    {
        $refreshToken = $this->repository->searchByTokenValue($token);

        if (null === $refreshToken) {
            throw new RefreshTokenNotFoundException();
        }

        $this->repository->delete($refreshToken);
    }
}