<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\RefreshToken\Application\Service;

use LaSalle\StudentTeacher\User\RefreshToken\Application\Request\DeleteRefreshTokenRequest;
use LaSalle\StudentTeacher\User\RefreshToken\Application\Service\RefreshTokenService;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Token;

final class DeleteRefreshTokenService extends RefreshTokenService
{
    public function __invoke(DeleteRefreshTokenRequest $request): void
    {
        $refreshTokenValue = new Token($request->getRefreshTokenValue());

        $refreshToken = $this->searchRefreshToken($refreshTokenValue);

        $this->refreshTokenRepository->delete($refreshToken);
    }
}