<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Token\Application\Request\SaveRefreshTokenRequest;
use LaSalle\StudentTeacher\Token\Application\Response\RefreshTokenResponse;
use LaSalle\StudentTeacher\Token\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\RefreshTokenString;

final class SaveRefreshToken
{
    private RefreshTokenRepository $refreshTokenRepository;

    public function __construct(RefreshTokenRepository $refreshTokenRepository)
    {
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    public function __invoke(SaveRefreshTokenRequest $request): RefreshTokenResponse
    {
        try {
            $userId = new Uuid($request->getUserId());
        } catch (InvalidUuidException $exception) {
            throw new InvalidArgumentValidationException($exception->getMessage());
        }

        $refreshTokenString = $this->refreshTokenRepository->nextIdentity();

        $refreshToken = new RefreshToken(
            $refreshTokenString,
            $userId,
            $request->getExpirationDate()
        );

        $this->refreshTokenRepository->save($refreshToken);

        return new RefreshTokenResponse(
            $refreshToken->getRefreshToken()->toString(),
            $refreshToken->getUserId()->toString(),
            $refreshToken->getExpirationDate()
        );
    }
}