<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserInformationRequest;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\CheckPermission;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidEmailException;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;

final class UpdateUserInformation
{
    private UserRepository $userRepository;
    private CheckPermission $security;

    public function __construct(UserRepository $userRepository, CheckPermission $security)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    public function __invoke(UpdateUserInformationRequest $request): void
    {
        $author = $this->userRepository->ofId($this->createIdFromPrimitive($request->getRequestAuthorId()));
        $userToUpdate = $this->userRepository->ofId($this->createIdFromPrimitive($request->getId()));
        $this->checkIfUserExists($userToUpdate);

        if (false === $this->security->isGranted('edit-user', $author, $userToUpdate)) {
            throw new PermissionDeniedException();
        }

        $this->checkIfNewEmailIsAvailable($request->getEmail(), $userToUpdate->getEmail()->toString());

        $userToUpdate->setEmail($this->createEmailFromPrimitive($request->getEmail()));
        $userToUpdate->setFirstName($request->getFirstName());
        $userToUpdate->setLastName($request->getLastName());
        $userToUpdate->setImage($request->getImage());
        $userToUpdate->setEducation($request->getEducation());
        $userToUpdate->setExperience($request->getExperience());

        $this->userRepository->save($userToUpdate);
    }

    private function createIdFromPrimitive(string $uuid): Uuid
    {
        try {
            return new Uuid($uuid);
        } catch (InvalidUuidException $error) {
            throw new InvalidArgumentValidationException($error->getMessage());
        }
    }

    private function checkIfUserExists(?User $user): void
    {
        if (null === $user) {
            throw new UserNotFoundException();
        }
    }

    private function createEmailFromPrimitive(string $email): Email
    {
        try {
            return new Email($email);
        } catch (InvalidEmailException $exception) {
            throw new InvalidArgumentValidationException($exception->getMessage());
        }
    }

    private function checkIfNewEmailIsAvailable(string $newEmail, string $oldEmail): void
    {
        $userWithNewEmail = $this->userRepository->ofEmail($this->createEmailFromPrimitive($newEmail));
        if (null !== $userWithNewEmail && $newEmail !== $oldEmail) {
            throw new UserAlreadyExistsException();
        }
    }
}