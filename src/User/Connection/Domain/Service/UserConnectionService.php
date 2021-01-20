<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Connection\Domain\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Connection\Application\Exception\ConnectionAlreadyExistsException;
use LaSalle\StudentTeacher\User\Connection\Application\Exception\ConnectionNotFoundException;
use LaSalle\StudentTeacher\User\User\Application\Exception\RolesOfUsersEqualException;
use LaSalle\StudentTeacher\User\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Connection\Domain\Aggregate\UserConnection;
use LaSalle\StudentTeacher\User\Connection\Domain\Repository\UserConnectionRepository;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Role;

final class UserConnectionService
{
    public function __construct(private UserConnectionRepository $repository)
    {
    }

    public function findUserConnection(User $student, User $teacher): UserConnection
    {
        $connection = $this->repository->ofId($student->getId(), $teacher->getId());
        if (null === $connection) {
            throw new ConnectionNotFoundException();
        }
        return $connection;
    }

    public function ensureConnectionDoesntExists(User $student, User $teacher): void
    {
        if (null !== $this->repository->ofId($student->getId(), $teacher->getId())) {
            throw new ConnectionAlreadyExistsException();
        }
    }

    public function identifyStudentAndTeacher(User $firstUser, User $secondUser): array
    {
        $firstUser->ensureUsersAreNotEqual($secondUser);
        $this->ensureRolesAreNotEqual($firstUser, $secondUser);

        return [Role::STUDENT, Role::TEACHER] === [
            $this->identifyIfTeacherOfStudent($firstUser),
            $this->identifyIfTeacherOfStudent($secondUser)
        ] ? [$firstUser, $secondUser] : [$secondUser, $firstUser];
    }

    private function ensureRolesAreNotEqual(User $firstUser, User $secondUser): void
    {
        if ($this->identifyIfTeacherOfStudent($firstUser) === $this->identifyIfTeacherOfStudent($secondUser)) {
            throw new RolesOfUsersEqualException();
        }
    }

    private function identifyIfTeacherOfStudent(User $user): string
    {
        if ($user->isInRole(new Role(Role::STUDENT))) {
            return Role::STUDENT;
        }
        if ($user->isInRole(new Role(Role::TEACHER))) {
            return Role::TEACHER;
        }
        throw new PermissionDeniedException();
    }

    public function identifySpecifier(User $authorId, User $firstUser, User $secondUser): User
    {
        if ($firstUser->idEqualsTo($authorId->getId())) {
            return $firstUser;
        }
        if ($secondUser->idEqualsTo($authorId->getId())) {
            return $secondUser;
        }
        throw new PermissionDeniedException();
    }

    public function verifySpecifierChanged(Uuid $newSpecifierId, Uuid $oldSpecifierId): bool
    {
        return (false === $newSpecifierId->equalsTo($oldSpecifierId));
    }
}
