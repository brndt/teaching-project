<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\RefreshToken\Save;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
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
        $refreshToken = new RefreshToken(
            Uuid::generate(),
            ($this->refreshTokenGenerating)(),
            Uuid::fromString($request->getUserId()),
            $request->getExpirationDate()
        );

        $this->repository->save($refreshToken);

        return new RefreshTokenResponse(
            $refreshToken->getId()->getValue(),
            $refreshToken->getRefreshToken(),
            $refreshToken->getUserId()->getValue(),
            $refreshToken->getExpirationDate()
        );
    }
}