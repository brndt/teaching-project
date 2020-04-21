<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Password\Update;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\OldPasswordIncorrectException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\Password;
use LaSalle\StudentTeacher\User\Domain\UserRepository;

final class UpdateUserPassword
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UpdateUserPasswordRequest $request): void
    {
        $userToUpdate = $this->repository->searchById(Uuid::fromString($request->getId()));

        if (null === $userToUpdate) {
            throw new UserNotFoundException();
        }

        if (false === Password::verify($request->getOldPassword(), $userToUpdate->getPassword())) {
            throw new OldPasswordIncorrectException();
        }

        $userToUpdate->setPassword(Password::fromPlainString($request->getNewPassword()));

        $this->repository->save($userToUpdate);
    }
}