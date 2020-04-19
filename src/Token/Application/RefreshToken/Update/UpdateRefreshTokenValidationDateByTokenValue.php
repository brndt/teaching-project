<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\RefreshToken\Update;

use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenIsInvalidException;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\RefreshToken\RefreshTokenResponse;
use LaSalle\StudentTeacher\Token\Domain\RefreshTokenRepository;

final class UpdateRefreshTokenValidationDateByTokenValue
{
    private RefreshTokenRepository $repository;

    public function __construct(RefreshTokenRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UpdateRefreshTokenValidationDateByTokenValueRequest $request): RefreshTokenResponse
    {
        $refreshToken = $this->repository->searchByTokenValue($request->getRefreshToken());

        if (null === $refreshToken) {
            throw new RefreshTokenNotFoundException();
        }

        if (!$refreshToken->isValid()) {
            throw new RefreshTokenIsInvalidException();
        }

        $refreshToken->setValid($request->getNewValidation());

        $this->repository->update($refreshToken);

        return new RefreshTokenResponse(
            $refreshToken->getUuid(),
            $refreshToken->getRefreshToken(),
            $refreshToken->getValid(),
            $refreshToken->getId()
        );
    }
}