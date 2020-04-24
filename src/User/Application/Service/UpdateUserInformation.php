<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserInformationRequest;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidEmailException;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;

final class UpdateUserInformation
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(UpdateUserInformationRequest $request): void
    {
        try {
            $userId = Uuid::fromString($request->getId());
        } catch (InvalidUuidException $error) {
            throw new InvalidArgumentValidationException($error->getMessage());
        }

        $userToUpdate = $this->userRepository->ofId($userId);

        if (null === $userToUpdate) {
            throw new UserNotFoundException();
        }

        try {
            $newEmail = new Email($request->getEmail());
        } catch (InvalidEmailException $exception) {
            throw new InvalidArgumentValidationException($exception->getMessage());
        }

        $userWithNewEmail = $this->userRepository->ofEmail($newEmail);

        if (null === $userWithNewEmail) {
            $userWithNewEmail = null;
        }

        if (null !== $userWithNewEmail && $request->getEmail() !== $userToUpdate->getEmail()->toString()
        ) {
            throw new UserAlreadyExistsException();
        }

        $userToUpdate->setEmail($newEmail);
        $userToUpdate->setFirstName($request->getFirstName());
        $userToUpdate->setLastName($request->getLastName());
        $userToUpdate->setImage($request->getImage());
        $userToUpdate->setEducation($request->getEducation());
        $userToUpdate->setExperience($request->getExperience());

        $this->userRepository->save($userToUpdate);
    }
}