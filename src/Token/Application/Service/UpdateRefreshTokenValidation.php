<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Service;

use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenIsExpiredException;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\Request\UpdateRefreshTokenValidationRequest;
use LaSalle\StudentTeacher\Token\Application\Response\RefreshTokenResponse;
use LaSalle\StudentTeacher\Token\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\RefreshTokenString;

final class UpdateRefreshTokenValidation
{
    private RefreshTokenRepository $refreshTokenRepository;

    public function __construct(RefreshTokenRepository $refreshTokenRepository)
    {
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    public function __invoke(UpdateRefreshTokenValidationRequest $request): RefreshTokenResponse
    {
        $refreshToken = $this->refreshTokenRepository->ofRefreshTokenString(
            RefreshTokenString::fromString($request->getRefreshToken())
        );

        if (null === $refreshToken) {
            throw new RefreshTokenNotFoundException();
        }

        if (true === $refreshToken->isExpired()) {
            throw new RefreshTokenIsExpiredException();
        }

        $refreshToken->setValid($request->getNewValidationDate());

        $this->refreshTokenRepository->save($refreshToken);

        return new RefreshTokenResponse(
            $refreshToken->getId()->toString(),
            $refreshToken->getRefreshToken()->toString(),
            $refreshToken->getUserId()->toString(),
            $refreshToken->getExpirationDate()
        );
    }
}