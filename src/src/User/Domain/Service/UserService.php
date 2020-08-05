<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;

final class UserService
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findUser(Uuid $id): User
    {
        $user = $this->repository->ofId($id);
        if (null === $user) {
            throw new UserNotFoundException();
        }
        return $user;
    }
}
