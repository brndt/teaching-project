<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\RefreshToken\Application\Service;

use LaSalle\StudentTeacher\User\RefreshToken\Application\Service\RefreshTokenService;
use LaSalle\StudentTeacher\User\RefreshToken\Application\Request\UpdateRefreshTokenExpirationRequest;
use LaSalle\StudentTeacher\User\RefreshToken\Application\Response\TokensResponse;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Token;

final class UpdateRefreshTokenExpirationService extends RefreshTokenService
{
    public function __invoke(UpdateRefreshTokenExpirationRequest $request): TokensResponse
    {
        $refreshToken = $this->refreshTokenRepository->ofToken(
            new Token($request->getRefreshToken())
        );

        $this->ensureRefreshTokenExists($refreshToken);
        $this->ensureRefreshTokenIsNotExpired($refreshToken);

        $refreshToken->setValid($request->getNewExpirationDate());

        $this->refreshTokenRepository->save($refreshToken);

        return new TokensResponse(
            $this->generateToken($refreshToken),
            $refreshToken->getRefreshToken()->toString(),
            $refreshToken->getUserId()->toString()
        );
    }
}
