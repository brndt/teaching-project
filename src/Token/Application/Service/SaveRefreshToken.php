<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Token\Application\Request\SaveRefreshTokenRequest;
use LaSalle\StudentTeacher\Token\Application\Response\RefreshTokenResponse;
use LaSalle\StudentTeacher\Token\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\RefreshTokenString;

final class SaveRefreshToken
{
    private RefreshTokenRepository $repository;

    public function __construct(RefreshTokenRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(SaveRefreshTokenRequest $request): RefreshTokenResponse
    {
        $refreshToken = new RefreshToken(
            Uuid::generate(),
            RefreshTokenString::generate(),
            Uuid::fromString($request->getUserId()),
            $request->getExpirationDate()
        );

        $this->repository->save($refreshToken);

        return new RefreshTokenResponse(
            $refreshToken->getId()->toString(),
            $refreshToken->getRefreshToken()->toString(),
            $refreshToken->getUserId()->toString(),
            $refreshToken->getExpirationDate()
        );
    }
}