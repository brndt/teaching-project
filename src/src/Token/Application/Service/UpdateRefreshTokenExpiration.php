<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Service;

use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenIsExpiredException;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\Request\UpdateRefreshTokenExpirationRequest;
use LaSalle\StudentTeacher\Token\Application\Response\RefreshTokenResponse;
use LaSalle\StudentTeacher\Token\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\RefreshTokenString;

final class UpdateRefreshTokenExpiration
{
    private RefreshTokenRepository $refreshTokenRepository;

    public function __construct(RefreshTokenRepository $refreshTokenRepository)
    {
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    public function __invoke(UpdateRefreshTokenExpirationRequest $request): RefreshTokenResponse
    {
        $refreshToken = $this->refreshTokenRepository->ofRefreshTokenString(
            new RefreshTokenString($request->getRefreshToken())
        );

        if (null === $refreshToken) {
            throw new RefreshTokenNotFoundException();
        }

        if (true === $refreshToken->isExpired()) {
            throw new RefreshTokenIsExpiredException();
        }

        $refreshToken->setValid($request->getNewExpirationDate());

        $this->refreshTokenRepository->save($refreshToken);

        return new RefreshTokenResponse(
            $refreshToken->getRefreshToken()->toString(),
            $refreshToken->getUserId()->toString(),
            $refreshToken->getExpirationDate()
        );
    }
}