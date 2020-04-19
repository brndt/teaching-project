<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\RefreshToken\Save;

use LaSalle\StudentTeacher\Token\Application\RefreshToken\RefreshTokenResponse;
use LaSalle\StudentTeacher\Token\Domain\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\RefreshTokenRepository;
use LaSalle\StudentTeacher\Token\Domain\RefreshTokenGenerating;

final class SaveRefreshToken
{
    private RefreshTokenRepository $repository;
    private RefreshTokenGenerating $refreshTokenGenerating;

    public function __construct(RefreshTokenRepository $repository, RefreshTokenGenerating $refreshTokenGenerating)
    {
        $this->repository = $repository;
        $this->refreshTokenGenerating = $refreshTokenGenerating;
    }

    public function __invoke(SaveRefreshTokenRequest $request): RefreshTokenResponse
    {
        $refreshToken = $this->repository->save(
            new RefreshToken($request->getUuid(), ($this->refreshTokenGenerating)(), $request->getValid())
        );

        return new RefreshTokenResponse(
            $refreshToken->getUuid(),
            $refreshToken->getRefreshToken(),
            $refreshToken->getValid(),
            $refreshToken->getId()
        );
    }
}