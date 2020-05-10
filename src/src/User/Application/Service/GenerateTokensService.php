<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\User\Application\Request\GenerateTokensRequest;
use LaSalle\StudentTeacher\User\Application\Response\TokensResponse;
use LaSalle\StudentTeacher\User\Domain\Aggregate\RefreshToken;

final class GenerateTokensService extends RefreshTokenService
{
    public function __invoke(GenerateTokensRequest $request): TokensResponse
    {
        $userId = $this->createIdFromPrimitive($request->getUserId());
        $expirationDate = $request->getExpirationDate();

        $refreshToken = new RefreshToken($this->refreshTokenRepository->nextIdentity(), $userId, $expirationDate);

        $this->refreshTokenRepository->save($refreshToken);

        return new TokensResponse($this->generateToken($refreshToken), $refreshToken->getRefreshToken()->toString());
    }
}