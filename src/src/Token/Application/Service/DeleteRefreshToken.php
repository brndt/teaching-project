<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Service;

use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\Request\DeleteRefreshTokenRequest;
use LaSalle\StudentTeacher\Token\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\Token;

final class DeleteRefreshToken
{
    private RefreshTokenRepository $refreshTokenRepository;

    public function __construct(RefreshTokenRepository $refreshTokenRepository)
    {
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    public function __invoke(DeleteRefreshTokenRequest $request): void
    {
        $refreshToken = $this->refreshTokenRepository->ofToken(
            new Token($request->getRefreshTokenValue())
        );

        $this->checkIfRefreshTokenExists($refreshToken);

        $this->refreshTokenRepository->delete($refreshToken);
    }

    private function checkIfRefreshTokenExists(?RefreshToken $refreshToken)
    {
        if (null === $refreshToken) {
            throw new RefreshTokenNotFoundException();
        }
    }
}