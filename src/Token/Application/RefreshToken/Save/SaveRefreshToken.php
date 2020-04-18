<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\RefreshToken\Save;

use LaSalle\StudentTeacher\Token\Application\RefreshToken\RefreshTokenResponse;
use LaSalle\StudentTeacher\Token\Domain\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\RefreshTokenRepository;

final class SaveRefreshToken
{
    private RefreshTokenRepository $repository;

    public function __construct(RefreshTokenRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(SaveRefreshTokenRequest $request): RefreshTokenResponse
    {
        $refreshToken = $this->repository->save(
            new RefreshToken($request->getUuid(), $request->getRefreshToken(), $request->getValid())
        );

        return new RefreshTokenResponse(
            $refreshToken->getUuid(),
            $refreshToken->getRefreshToken(),
            $refreshToken->getValid(),
            $refreshToken->getId()
        );
    }
}