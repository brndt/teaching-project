<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\UpdateBasicUserInformationRequest;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidEmailException;
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

        try {
            $email = new Email($request->getEmail());
        } catch (InvalidEmailException $exception) {
            throw new InvalidArgumentValidationException($exception->getMessage());
        }

        $userWithNewEmail = $this->repository->searchByEmail($email);

        if (null === $userWithNewEmail) {
            $userWithNewEmail = null;
        }

        if (null !== $userWithNewEmail && $request->getEmail() !== $userToUpdate->getEmail()->toString()
        ) {
            throw new UserAlreadyExistsException();
        }

        $userToUpdate->setEmail($email);
        $userToUpdate->setFirstName($request->getFirstName());
        $userToUpdate->setLastName($request->getLastName());
        $userToUpdate->setImage($request->getImage());
        $userToUpdate->setEducation($request->getEducation());
        $userToUpdate->setExperience($request->getExperience());

        $this->repository->save($userToUpdate);
    }
}