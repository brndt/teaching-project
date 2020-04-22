<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Service;

use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\Request\DeleteRefreshTokenByTokenValueRequest;
use LaSalle\StudentTeacher\Token\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\RefreshTokenString;

final class DeleteRefreshTokenByTokenValue
{
    private RefreshTokenRepository $repository;

    public function __construct(RefreshTokenRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(DeleteRefreshTokenByTokenValueRequest $request): void
    {
        $refreshToken = $this->repository->searchByTokenValue(
            RefreshTokenString::fromString($request->getRefreshTokenValue())
        );

        if (null === $refreshToken) {
            throw new RefreshTokenNotFoundException();
        }

        $this->repository->delete($refreshToken);
    }
}