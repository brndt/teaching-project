<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\BasicUserInformation\Update;

use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
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

        $userToUpdate->setEmail($request->getEmail());
        $userToUpdate->setFirstName($request->getFirstName());
        $userToUpdate->setLastName($request->getLastName());
        $userToUpdate->setImage($request->getImage());
        $userToUpdate->setEducation($request->getEducation());
        $userToUpdate->setExperience($request->getExperience());

        $this->repository->updateBasicInformation($userToUpdate);
    }
}