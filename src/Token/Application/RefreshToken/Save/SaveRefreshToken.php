<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\RefreshToken\Save;

use LaSalle\StudentTeacher\Token\Application\RefreshToken\RefreshTokenResponse;
use LaSalle\StudentTeacher\Token\Domain\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\RefreshTokenGenerating;
use LaSalle\StudentTeacher\Token\Domain\RefreshTokenRepository;

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
        $refreshToken = new RefreshToken($request->getUuid(), ($this->refreshTokenGenerating)(), $request->getValid());

        $this->repository->save($refreshToken);

        return new RefreshTokenResponse(
            $refreshToken->getUuid(),
            $refreshToken->getRefreshToken(),
            $refreshToken->getValid(),
        );
    }
}