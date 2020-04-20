<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Password\Update;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\OldPasswordIncorrectException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\PasswordHashing;
use LaSalle\StudentTeacher\User\Domain\UserRepository;

final class UpdateUserPassword
{
    private UserRepository $repository;
    private PasswordHashing $passwordHashing;

    public function __construct(UserRepository $repository, PasswordHashing $passwordHashing)
    {
        $this->repository = $repository;
        $this->passwordHashing = $passwordHashing;
    }

    public function __invoke(UpdateUserPasswordRequest $request): void
    {
        $userToUpdate = $this->repository->searchById(Uuid::fromString($request->getId()));

        if (null === $userToUpdate) {
            throw new UserNotFoundException();
        }

        if (false === $this->passwordHashing->verify($request->getOldPassword(), $userToUpdate->getPassword())) {
            throw new OldPasswordIncorrectException();
        }

        $userToUpdate->setPassword($this->passwordHashing->hash_password($request->getNewPassword()));

        $this->repository->save($userToUpdate);
    }
}