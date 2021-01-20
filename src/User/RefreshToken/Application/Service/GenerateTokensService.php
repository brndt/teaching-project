<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\RefreshToken\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\RefreshToken\Application\Request\GenerateTokensRequest;
use LaSalle\StudentTeacher\User\RefreshToken\Application\Response\TokensResponse;
use LaSalle\StudentTeacher\User\RefreshToken\Application\Service\RefreshTokenService;
use LaSalle\StudentTeacher\User\RefreshToken\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Token;

final class GenerateTokensService extends RefreshTokenService
{
    public function __invoke(GenerateTokensRequest $request): TokensResponse
    {
        $userId = new Uuid($request->getUserId());
        $expirationDate = $request->getExpirationDate();

        $token = new Token($this->randomStringGenerator->generate());

        $refreshToken = new RefreshToken($token, $userId, $expirationDate);

        $this->refreshTokenRepository->save($refreshToken);

        return new TokensResponse(
            $this->generateToken($refreshToken),
            $refreshToken->getRefreshToken()->toString(),
            $refreshToken->getUserId()->toString()
        );
    }
}
