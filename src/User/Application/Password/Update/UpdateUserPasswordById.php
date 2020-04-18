<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Password\Update;

use LaSalle\StudentTeacher\User\Application\Exception\OldPasswordIncorrectException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\UserRepository;

final class UpdateUserPasswordById
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UpdateUserPasswordByIdRequest $request): void
    {
        $userToUpdate = $this->repository->searchById($request->getId());

        if (null === $userToUpdate) {
            throw new UserNotFoundException();
        }

        if (false === password_verify($request->getOldPassword(), $userToUpdate->getPassword())) {
            throw new OldPasswordIncorrectException();
        }

        $userToUpdate->setPassword(password_hash($request->getNewPassword(), PASSWORD_DEFAULT));

        $this->repository->updatePassword($userToUpdate);
    }
}