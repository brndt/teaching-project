<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Service;

use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\Request\DeleteRefreshTokenRequest;
use LaSalle\StudentTeacher\Token\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\RefreshTokenString;

final class DeleteRefreshToken
{
    private RefreshTokenRepository $refreshTokenRepository;

    public function __construct(RefreshTokenRepository $refreshTokenRepository)
    {
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    public function __invoke(DeleteRefreshTokenRequest $request): void
    {
        $refreshToken = $this->refreshTokenRepository->ofRefreshTokenString(
            RefreshTokenString::fromString($request->getRefreshTokenValue())
        );

        if (null === $refreshToken) {
            throw new RefreshTokenNotFoundException();
        }

        $this->refreshTokenRepository->delete($refreshToken);
    }
}