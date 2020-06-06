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
        if (true === $firstUser->idEqualsTo($secondUser->getId())) {
            throw new UsersAreEqualException();
        }
    }

    protected function identifyIfTeacherOfStudent(User $user): string
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
        if ($this->identifyIfTeacherOfStudent($firstUser) === $this->identifyIfTeacherOfStudent($secondUser)) {
            throw new RolesOfUsersEqualException();
        }
    }

    protected function ensureUserExists(?User $user): void
    {
        if (null === $user) {
            throw new UserNotFoundException();
        }
    }

    protected function identifyStudentAndTeacher(User $firstUser, User $secondUser): array
    {
        $this->ensureUsersAreNotEqual($firstUser, $secondUser);
        $this->ensureRolesAreNotEqual($firstUser, $secondUser);

        return [Role::STUDENT, Role::TEACHER] === [
            $this->identifyIfTeacherOfStudent($firstUser),
            $this->identifyIfTeacherOfStudent($secondUser)
        ] ? [$firstUser, $secondUser] : [$secondUser, $firstUser];
    }

    protected function ensureConnectionDoesntExists(User $student, User $teacher): void
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

    protected function verifySpecifierChanged(Uuid $newSpecifierId, Uuid $oldSpecifierId): bool
    {
        return (false === $newSpecifierId->equalsTo($oldSpecifierId));
    }

    protected function identifySpecifier(Uuid $authorId, User $firstUser, User $secondUser): User
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

    protected function ensureRequestAuthorIsOneOfUsers(User $author, User $firstUser, User $secondUser): void
    {
        if (false === $author->idEqualsTo($firstUser->getId()) && false === $author->idEqualsTo($secondUser->getId())) {
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
