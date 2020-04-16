<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\RefreshToken\Save;

use LaSalle\StudentTeacher\Token\Domain\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\RefreshTokenRepository;

final class SaveRefreshToken
{
    private RefreshTokenRepository $repository;

    public function __construct(RefreshTokenRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke($username, $valid): RefreshToken
    {
        $token = new RefreshToken($username, $valid);
        return $this->repository->save($token);
    }
}