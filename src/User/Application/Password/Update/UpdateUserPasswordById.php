<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Password\Update;

use LaSalle\StudentTeacher\User\Application\Exception\OldPasswordIncorrectException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\PasswordHashing;
use LaSalle\StudentTeacher\User\Domain\UserRepository;

final class UpdateUserPasswordById
{
    private UserRepository $repository;
    private PasswordHashing $passwordHashing;

    public function __construct(UserRepository $repository, PasswordHashing $passwordHashing)
    {
        $this->repository = $repository;
        $this->passwordHashing = $passwordHashing;
    }

    public function __invoke(UpdateUserPasswordByIdRequest $request): void
    {
        $userToUpdate = $this->repository->searchById($request->getId());

        if (null === $userToUpdate) {
            throw new UserNotFoundException();
        }

        if (false === $this->passwordHashing->verify($request->getOldPassword(), $userToUpdate->getPassword())) {
            throw new OldPasswordIncorrectException();
        }

        $userToUpdate->setPassword($this->passwordHashing->hash_password($request->getNewPassword()));

        $this->repository->updatePassword($userToUpdate);
    }
}