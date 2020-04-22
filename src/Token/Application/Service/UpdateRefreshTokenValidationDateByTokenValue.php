<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Service;

use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenIsExpiredException;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\Request\UpdateRefreshTokenValidationDateByTokenValueRequest;
use LaSalle\StudentTeacher\Token\Application\Response\RefreshTokenResponse;
use LaSalle\StudentTeacher\Token\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\RefreshTokenString;

final class UpdateRefreshTokenValidationDateByTokenValue
{
    private RefreshTokenRepository $repository;

    public function __construct(RefreshTokenRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UpdateRefreshTokenValidationDateByTokenValueRequest $request): RefreshTokenResponse
    {
        $refreshToken = $this->repository->searchByTokenValue(
            RefreshTokenString::fromString($request->getRefreshToken())
        );

        if (null === $refreshToken) {
            throw new RefreshTokenNotFoundException();
        }

        if (true === $refreshToken->isExpired()) {
            throw new RefreshTokenIsExpiredException();
        }

        $refreshToken->setValid($request->getNewValidationDate());

        $this->repository->save($refreshToken);

        return new RefreshTokenResponse(
            $refreshToken->getId()->toString(),
            $refreshToken->getRefreshToken()->toString(),
            $refreshToken->getUserId()->toString(),
            $refreshToken->getExpirationDate()
        );
    }
}