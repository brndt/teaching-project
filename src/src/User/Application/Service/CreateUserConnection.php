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
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Aggregate\UserConnection;
use LaSalle\StudentTeacher\User\Domain\Repository\UserConnectionRepository;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\Domain\ValueObject\State\Pended;

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
        $this->ensureRequestAuthorCanExecute($request->getRequestAuthorId(), $request->getFirstUser());

        $authorId = $this->createIdFromPrimitive($request->getRequestAuthorId());
        $firstUser = $this->identifyUserById($request->getFirstUser());
        $secondUser = $this->identifyUserById($request->getSecondUser());

        [$student, $teacher] = $this->verifyStudentAndTeacher($firstUser, $secondUser);
        $this->ensureConnectionIsNotAlreadyExists($student, $teacher);

        $userConnection = new UserConnection($student->getId(), $teacher->getId(), new Pended(), $authorId);

        $this->userConnectionRepository->save($userConnection);
    }

    private function ensureRequestAuthorCanExecute(string $requestAuthorId, string $firstUser): void
    {
        if ($requestAuthorId !== $firstUser) {
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

    private function ensureConnectionIsNotAlreadyExists(User $student, User $teacher)
    {
        if (null !== $this->userConnectionRepository->ofId($student->getId(), $teacher->getId())) {
            throw new ConnectionAlreadyExistsException();
        }
    }
}