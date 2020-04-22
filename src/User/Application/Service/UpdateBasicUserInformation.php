<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\UpdateBasicUserInformationRequest;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;

final class UpdateBasicUserInformation
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UpdateBasicUserInformationRequest $request): void
    {
        $userToUpdate = $this->repository->searchById(Uuid::fromString($request->getId()));

        if (null === $userToUpdate) {
            throw new UserNotFoundException();
        }

        $userWithNewEmail = $this->repository->searchByEmail(new Email($request->getEmail()));

        if (null === $userWithNewEmail) {
            $userWithNewEmail = null;
        }

        if (null !== $userWithNewEmail && $request->getEmail() !== $userToUpdate->getEmail()->toString()
        ) {
            throw new UserAlreadyExistsException();
        }

        $userToUpdate->setEmail(new Email($request->getEmail()));
        $userToUpdate->setFirstName($request->getFirstName());
        $userToUpdate->setLastName($request->getLastName());
        $userToUpdate->setImage($request->getImage());
        $userToUpdate->setEducation($request->getEducation());
        $userToUpdate->setExperience($request->getExperience());

        $this->repository->save($userToUpdate);
    }
}