<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\ConnectionAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Exception\RoleIsNotStudentOrTeacherException;
use LaSalle\StudentTeacher\User\Application\Exception\RolesOfUsersEqualException;
use LaSalle\StudentTeacher\User\Application\Exception\UserAreEqualException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\CreateUserConnectionRequest;
use LaSalle\StudentTeacher\User\Domain\Aggregate\UserConnection;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserConnectionRepository;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\RequestStatus;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;

final class CreateUserConnection
{
    private UserRepository $userRepository;
    private UserConnectionRepository $userConnectionRepository;

    public function __construct(
        UserRepository $userRepository,
        UserConnectionRepository $userConnectionRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userConnectionRepository = $userConnectionRepository;
    }

    public function __invoke(CreateUserConnectionRequest $request): void
    {
        $this->ensureRequestAuthorCanExecute($request->getRequestAuthorId(), $request->getUserId());

        $user = $this->identifyUserById($request->getUserId());
        $friend = $this->identifyUserById($request->getFriendId());

        $this->ensureRolesAndUsersAreNotEqual($user, $friend);
        $this->ensureConnectionIsNotAlreadyExists($user, $friend);

        $userConnection = new UserConnection(
            $user->getId(),
            $friend->getId(),
            new RequestStatus(RequestStatus::STATUS_PENDING)
        );

        $this->userConnectionRepository->save($userConnection);
    }

    private function ensureRequestAuthorCanExecute(string $requestAuthorId, string $userId): void {
        if ($requestAuthorId !== $userId) {
            throw new PermissionDeniedException();
        }
    }

    private function identifyUserById(string $id): User
    {
        $user = $this->userRepository->ofId($this->createIdFromPrimitive($id));
        if (null === $user) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    private function createIdFromPrimitive(string $uuid): Uuid
    {
        try {
            return new Uuid($uuid);
        } catch (InvalidUuidException $error) {
            throw new InvalidArgumentValidationException($error->getMessage());
        }
    }

    private function ensureRolesAndUsersAreNotEqual(User $requestingUser, User $pendingUser): void
    {
        if ($requestingUser->getId()->toString() === $pendingUser->getId()->toString()) {
            throw new UserAreEqualException();
        }
        if ($this->verifyRole($requestingUser) === $this->verifyRole($pendingUser)) {
            throw new RolesOfUsersEqualException();
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

    private function ensureConnectionIsNotAlreadyExists(User $student, User $teacher)
    {
        if (null !== $this->userConnectionRepository->ofId($student->getId(), $teacher->getId())) {
            throw new ConnectionAlreadyExistsException();
        }
    }
}