<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\RefreshToken\Search;

use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\RefreshToken\RefreshTokenResponse;
use LaSalle\StudentTeacher\Token\Domain\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\RefreshTokenRepository;

final class SearchRefreshTokenByTokenValue
{
    private RefreshTokenRepository $repository;

    public function __construct(RefreshTokenRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(string $token): RefreshTokenResponse
    {
        $refreshToken = $this->repository->searchByTokenValue($token);

        if (null === $refreshToken) {
            throw new RefreshTokenNotFoundException();
        }

        return new RefreshTokenResponse(
            $refreshToken->getUuid(),
            $refreshToken->getRefreshToken(),
            $refreshToken->getValid(),
            $refreshToken->getId()
        );
    }
}