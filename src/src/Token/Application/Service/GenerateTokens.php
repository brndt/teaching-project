<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Token\Application\Request\GenerateTokensRequest;
use LaSalle\StudentTeacher\Token\Application\Response\TokensResponse;
use LaSalle\StudentTeacher\Token\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\Token\Domain\TokenManager;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;

final class GenerateTokens
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

    public function __invoke(GenerateTokensRequest $request): TokensResponse
    {
        $refreshToken = new RefreshToken(
            $this->refreshTokenRepository->nextIdentity(),
            $this->createIdFromPrimitive($request->getUserId()),
            $request->getExpirationDate()
        );

        $this->refreshTokenRepository->save($refreshToken);

        return new TokensResponse($this->generateTokenFromUserId($refreshToken->getUserId()), $refreshToken->getRefreshToken()->toString());
    }

    private function createIdFromPrimitive(string $uuid): Uuid
    {
        try {
            return new Uuid($uuid);
        } catch (InvalidUuidException $error) {
            throw new InvalidArgumentValidationException($error->getMessage());
        }
    }

    private function generateTokenFromUserId(Uuid $id): string {
        $user = $this->userRepository->ofId($id);
        return $this->tokenManager->generate($user);
    }
}