<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\RefreshTokenIsExpiredException;
use LaSalle\StudentTeacher\User\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\User\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\User\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\TokenManager;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

abstract class RefreshTokenService
{
    protected RefreshTokenRepository $refreshTokenRepository;
    protected TokenManager $tokenManager;
    protected UserRepository $userRepository;

    public function __construct(
        RefreshTokenRepository $refreshTokenRepository,
        TokenManager $tokenManager,
        UserRepository $userRepository
    ) {
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->tokenManager = $tokenManager;
        $this->userRepository = $userRepository;
    }

    protected function createIdFromPrimitive(string $uuid): Uuid
    {
        try {
            return new Uuid($uuid);
        } catch (InvalidUuidException $error) {
            throw new InvalidArgumentException($error->getMessage());
        }
    }

    protected function ensureRefreshTokenExists(?RefreshToken $refreshToken)
    {
        if (null === $refreshToken) {
            throw new RefreshTokenNotFoundException();
        }
    }

    protected function ensureRefreshTokenIsNotExpired(RefreshToken $refreshToken)
    {
        if (true === $refreshToken->isExpired()) {
            throw new RefreshTokenIsExpiredException();
        }
    }

    protected function generateToken(RefreshToken $refreshToken): string
    {
        $user = $this->userRepository->ofId($refreshToken->getUserId());
        return $this->tokenManager->generate($user);
    }

    protected function searchRefreshToken(Token $token) {
        if (null === $user = $this->refreshTokenRepository->ofToken($token)) {
            throw new RefreshTokenNotFoundException();
        }
        return $user;
    }
}