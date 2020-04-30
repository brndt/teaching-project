<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Service;

use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenIsExpiredException;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\Request\RefreshTokensRequest;
use LaSalle\StudentTeacher\Token\Application\Response\TokensResponse;
use LaSalle\StudentTeacher\Token\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\Token\Domain\TokenManager;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\Token;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;

final class UpdateRefreshTokensExpiration
{
    private RefreshTokenRepository $refreshTokenRepository;
    private TokenManager $tokenManager;
    private UserRepository $userRepository;

    public function __construct(
        RefreshTokenRepository $refreshTokenRepository,
        TokenManager $tokenManager,
        UserRepository $userRepository
    ) {
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->tokenManager = $tokenManager;
        $this->userRepository = $userRepository;
    }

    public function __invoke(RefreshTokensRequest $request): TokensResponse
    {
        $refreshToken = $this->refreshTokenRepository->ofToken(
            new Token($request->getRefreshToken())
        );

        $this->checkIfRefreshTokenExists($refreshToken);
        $this->checkIfRefreshTokenExpired($refreshToken);

        $refreshToken->setValid($request->getNewExpirationDate());

        $this->refreshTokenRepository->save($refreshToken);

        return new TokensResponse($this->generateToken($refreshToken), $refreshToken->getRefreshToken()->toString());
    }

    private function checkIfRefreshTokenExists(RefreshToken $refreshToken)
    {
        if (null === $refreshToken) {
            throw new RefreshTokenNotFoundException();
        }
    }

    private function checkIfRefreshTokenExpired(RefreshToken $refreshToken)
    {
        if (true === $refreshToken->isExpired()) {
            throw new RefreshTokenIsExpiredException();
        }
    }

    private function generateToken(RefreshToken $refreshToken): string
    {
        $user = $this->userRepository->ofId($refreshToken->getUserId());
        return $this->tokenManager->generate($user);
    }
}