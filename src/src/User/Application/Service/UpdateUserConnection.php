<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\ConnectionNotFound;
use LaSalle\StudentTeacher\User\Application\Exception\RoleIsNotStudentOrTeacherException;
use LaSalle\StudentTeacher\User\Application\Exception\RolesOfUsersEqualException;
use LaSalle\StudentTeacher\User\Application\Exception\UserAreEqualException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserConnectionRequest;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserConnectionRepository;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\Domain\ValueObject\State\StateFactory;

final class UpdateUserConnection
{
    private UserRepository $userRepository;
    private UserConnectionRepository $userConnectionRepository;
    private StateFactory $stateFactory;

    public function __construct(
        UserRepository $userRepository,
        UserConnectionRepository $userConnectionRepository,
        StateFactory $stateFactory
    ) {
        $this->userRepository = $userRepository;
        $this->userConnectionRepository = $userConnectionRepository;
        $this->stateFactory = $stateFactory;
    }

    public function __invoke(UpdateUserConnectionRequest $request)
    {
        $this->ensureRequestAuthorCanExecute($request->getRequestAuthorId(), $request->getFirstUserId());

        $author = $this->verifyWhoReacts(
            $request->getRequestAuthorId(),
            $request->getFirstUserId(),
            $request->getSecondUserId()
        );

        $firstUser = $this->identifyUserById($request->getFirstUserId());
        $secondUser = $this->identifyUserById($request->getSecondUserId());

        [$student, $teacher] = $this->verifyStudentAndTeacher($firstUser, $secondUser);

        $userConnection = $this->userConnectionRepository->ofId($student->getId(), $teacher->getId());

        if (null === $userConnection) {
            throw new ConnectionNotFound();
        }

        $newState = $this->stateFactory->create($request->getStatus());
        $isSpecifierChanged = $this->verifySpecifierChanged($author, $userConnection->getSpecifierId());

        $userConnection->setState($newState, $isSpecifierChanged);
        $userConnection->setSpecifierId($author);

        $this->userConnectionRepository->save($userConnection);
    }

    private function ensureRequestAuthorCanExecute(string $requestAuthorId, string $userId): void
    {
        if ($requestAuthorId !== $userId) {
            throw new PermissionDeniedException();
        }
    }

    private function verifySpecifierChanged(Uuid $newSpecifier, Uuid $oldSpecifier) {
        return $oldSpecifier->toString() !== $newSpecifier->toString();
    }

    private function verifyWhoReacts($authorId, $firstUserId, $secondUserId): Uuid
    {
        if ($authorId === $firstUserId) {
            return $this->createIdFromPrimitive($firstUserId);
        }
        if ($authorId === $secondUserId) {
            return $this->createIdFromPrimitive($secondUserId);
        }
        throw new PermissionDeniedException();
    }

    private function identifyUserById(string $id): User
    {
        $user = $this->userRepository->ofId($this->createIdFromPrimitive($id));
        if (null === $user) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    private function verifyStudentAndTeacher(User $firstUser, User $secondUser)
    {
        $this->ensureUsersAreNotEqual($firstUser, $secondUser);
        $this->ensureRolesAreNotEqual($firstUser, $secondUser);
        return [Role::STUDENT, Role::TEACHER] === [
            $this->verifyRole($firstUser),
            $this->verifyRole($secondUser)
        ] ? [$firstUser, $secondUser] : [$secondUser, $firstUser];
    }

    private function ensureRolesAreNotEqual(User $firstUser, User $secondUser): void
    {
        if ($this->verifyRole($firstUser) === $this->verifyRole($secondUser)) {
            throw new RolesOfUsersEqualException();
        }
    }

    private function ensureUsersAreNotEqual(User $firstUser, User $secondUser): void
    {
        if ($firstUser->getId()->toString() === $secondUser->getId()->toString()) {
            throw new UserAreEqualException();
        }
    }

    private function verifyRole($user): string
    {
        if (in_array(Role::STUDENT, $user->getRoles()->toArrayOfPrimitives())) {
            return Role::STUDENT;
        }
        if (in_array(Role::TEACHER, $user->getRoles()->toArrayOfPrimitives())) {
            return Role::TEACHER;
        }
        throw new RoleIsNotStudentOrTeacherException();
    }

    private function createIdFromPrimitive(string $uuid): Uuid
    {
        try {
            return new Uuid($uuid);
        } catch (InvalidUuidException $error) {
            throw new InvalidArgumentValidationException($error->getMessage());
        }
    }
}