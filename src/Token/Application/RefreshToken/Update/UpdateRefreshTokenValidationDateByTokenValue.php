<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\RefreshToken\Update;

use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Domain\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\RefreshTokenRepository;

final class UpdateRefreshTokenValidationDateByTokenValue
{
    private RefreshTokenRepository $repository;

    public function __construct(RefreshTokenRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(\DateTime $newValidation, string $token): RefreshToken
    {
        $refreshToken = $this->repository->searchByTokenValue($token);

        if (null === $refreshToken) {
            throw new RefreshTokenNotFoundException();
        }

        return $this->repository->updateValidation($newValidation, $token);

    }
}