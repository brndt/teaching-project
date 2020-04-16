<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\BasicUserInformation\Update;

use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\User;
use LaSalle\StudentTeacher\User\Domain\UserRepository;

final class UpdateBasicUserInformationById
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UpdateBasicUserInformationByIdRequest $request): void
    {
        $userToUpdate = $this->repository->searchById($request->getId());

        if (null === $userToUpdate) {
            throw new UserNotFoundException();
        }

        $userWithNewEmail = $this->repository->searchByEmail($request->getEmail());

        if (null === $userWithNewEmail) {
            $userWithNewEmail = null;
        }

        if (null !== $userWithNewEmail && $request->getEmail() !== $userToUpdate->getEmail()
        ) {
            throw new UserAlreadyExistsException();
        }

        $user = new User();
        $user->setId($request->getId());
        $user->setEmail($request->getEmail());
        $user->setFirstName($request->getFirstName());
        $user->setLastName($request->getLastName());
        $user->setImage($request->getImage());
        $user->setEducation($request->getEducation());
        $user->setExperience($request->getExperience());

        $this->repository->updateBasicInformation($user);
    }
}