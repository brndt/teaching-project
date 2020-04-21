<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\RefreshToken\Update;

use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenIsExpiredException;
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

        if (true === $refreshToken->isExpired()) {
            throw new RefreshTokenIsExpiredException();
        }

        $refreshToken->setValid($request->getNewValidationDate());

        $this->repository->save($refreshToken);

        return new RefreshTokenResponse(
            $refreshToken->getId()->toPrimitives(),
            $refreshToken->getRefreshToken(),
            $refreshToken->getUserId()->toPrimitives(),
            $refreshToken->getExpirationDate()
        );
    }
}