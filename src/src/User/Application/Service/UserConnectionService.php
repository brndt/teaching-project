<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\ConnectionAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Exception\ConnectionNotFoundException;
use LaSalle\StudentTeacher\User\Application\Exception\RolesOfUsersEqualException;
use LaSalle\StudentTeacher\User\Application\Exception\UsersAreEqualException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Aggregate\UserConnection;
use LaSalle\StudentTeacher\User\Domain\Repository\UserConnectionRepository;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\Domain\ValueObject\State\StateFactory;

abstract class UserConnectionService
{
    protected UserRepository $userRepository;
    protected UserConnectionRepository $userConnectionRepository;
    protected StateFactory $stateFactory;

    public function __construct(
        UserConnectionRepository $userConnectionRepository,
        UserRepository $userRepository,
        StateFactory $stateFactory
    ) {
        $this->userRepository = $userRepository;
        $this->userConnectionRepository = $userConnectionRepository;
        $this->stateFactory = $stateFactory;
    }

    protected function createIdFromPrimitive(string $uuid): Uuid
    {
        try {
            return new Uuid($uuid);
        } catch (InvalidUuidException $error) {
            throw new InvalidArgumentException($error->getMessage());
        }
    }

    protected function ensureUsersAreNotEqual(User $firstUser, User $secondUser): void
    {
        if ($firstUser->getId()->toString() === $secondUser->getId()->toString()) {
            throw new UsersAreEqualException();
        }
    }

    protected function verifyRole(User $user): string
    {
        if ($user->isInRole(new Role(Role::STUDENT))) {
            return Role::STUDENT;
        }
        if ($user->isInRole(new Role(Role::TEACHER))) {
            return Role::TEACHER;
        }
        throw new PermissionDeniedException();
    }

    protected function ensureRolesAreNotEqual(User $firstUser, User $secondUser): void
    {
        if ($this->verifyRole($firstUser) === $this->verifyRole($secondUser)) {
            throw new RolesOfUsersEqualException();
        }
    }

    protected function searchUserById(Uuid $id): User
    {
        if (null === $user = $this->userRepository->ofId($id)) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    protected function verifyStudentAndTeacher(User $firstUser, User $secondUser)
    {
        $this->ensureUsersAreNotEqual($firstUser, $secondUser);
        $this->ensureRolesAreNotEqual($firstUser, $secondUser);

        return [Role::STUDENT, Role::TEACHER] === [
            $this->verifyRole($firstUser),
            $this->verifyRole($secondUser)
        ] ? [$firstUser, $secondUser] : [$secondUser, $firstUser];
    }

    protected function ensureConnectionDoesntExists(User $student, User $teacher)
    {
        if (null !== $this->userConnectionRepository->ofId($student->getId(), $teacher->getId())) {
            throw new ConnectionAlreadyExistsException();
        }
    }

    protected function ensureConnectionExists(?UserConnection $userConnection): void
    {
        if (null === $userConnection) {
            throw new ConnectionNotFoundException();
        }
    }

    protected function ensureConnectionsExist(?array $connections): void
    {
        if (true === empty($connections)) {
            throw new ConnectionNotFoundException();
        }
    }

    protected function verifySpecifierChanged(User $newSpecifier, User $oldSpecifier)
    {
        return $newSpecifier->idEqualsTo($oldSpecifier->getId());
    }

    protected function recognizeSpecifier(Uuid $authorId, User $firstUser, User $secondUser): User
    {
        if ($firstUser->idEqualsTo($authorId)) {
            return $firstUser;
        }
        if ($secondUser->idEqualsTo($authorId)) {
            return $secondUser;
        }
        throw new PermissionDeniedException();
    }

    protected function createFiltersByUserId(User $user): array
    {
        if (true === $user->isInRole(new Role(Role::STUDENT))) {
            return [['field' => 'studentId', 'operator' => '=', 'value' => $user->getId()->toString()]];
        }
        return [['field' => 'teacherId', 'operator' => '=', 'value' => $user->getId()->toString()]];
    }

    protected function ensureRequestAuthorIsTeacherOrStudent(User $author, User $user, User $friend)
    {
        if (false === $author->idEqualsTo($user->getId()) && false === $author->idEqualsTo($friend->getId())) {
            throw new PermissionDeniedException();
        }
    }

    protected function ensureRequestAuthorHasPermissions(User $author, User $user): void
    {
        if (false === $author->isInRole(new Role('admin')) && false === $author->idEqualsTo($user->getId())) {
            throw new PermissionDeniedException();
        }
    }
}