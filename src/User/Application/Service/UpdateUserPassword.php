<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\OldPasswordIncorrectException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserPasswordRequest;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;

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

        $userToUpdate->setPassword(Password::fromPlainPassword($request->getNewPassword()));

        $this->repository->save($userToUpdate);
    }
}