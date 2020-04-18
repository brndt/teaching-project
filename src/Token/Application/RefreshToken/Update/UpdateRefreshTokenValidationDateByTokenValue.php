<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\RefreshToken\Update;

use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenIsInvalidException;
use LaSalle\StudentTeacher\Token\Application\RefreshToken\RefreshTokenResponse;
use LaSalle\StudentTeacher\Token\Domain\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\RefreshTokenRepository;

final class UpdateRefreshTokenValidationDateByTokenValue
{
    private RefreshTokenRepository $repository;

    public function __construct(RefreshTokenRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(\DateTime $newValidation, string $token): RefreshTokenResponse
    {
        $refreshToken = $this->repository->searchByTokenValue($token);

        if (null === $refreshToken || !$refreshToken->isValid()) {
            throw new RefreshTokenIsInvalidException();
        }

        $refreshTokenUpdated = $this->repository->updateValidation($newValidation, $token);

        return new RefreshTokenResponse(
            $refreshTokenUpdated->getUuid(),
            $refreshTokenUpdated->getRefreshToken(),
            $refreshTokenUpdated->getValid(),
            $refreshTokenUpdated->getId()
        );

    }
}