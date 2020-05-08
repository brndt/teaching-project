<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\ConnectionAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Exception\ConnectionNotFound;
use LaSalle\StudentTeacher\User\Application\Exception\RoleIsNotStudentOrTeacherException;
use LaSalle\StudentTeacher\User\Application\Exception\RolesOfUsersEqualException;
use LaSalle\StudentTeacher\User\Application\Exception\UserAreEqualException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserConnectionRepository;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\Domain\ValueObject\State\StateFactory;

abstract class UserConnectionService
{
    protected UserRepository $userRepository;
    protected UserConnectionRepository $userConnectionRepository;
    protected StateFactory $stateFactory;

    public function __construct(UserConnectionRepository $userConnectionRepository, UserRepository $userRepository, StateFactory $stateFactory)
    {
        $this->userRepository = $userRepository;
        $this->userConnectionRepository = $userConnectionRepository;
        $this->stateFactory = $stateFactory;
    }

    protected function createIdFromPrimitive(string $uuid): Uuid
    {
        try {
            return new Uuid($uuid);
        } catch (InvalidUuidException $error) {
            throw new InvalidArgumentValidationException($error->getMessage());
        }
    }

    protected function ensureUsersAreNotEqual(User $firstUser, User $secondUser): void
    {
        if ($firstUser->getId()->toString() === $secondUser->getId()->toString()) {
            throw new UserAreEqualException();
        }
    }

    protected function verifyRole($user): string
    {
        if (in_array(Role::STUDENT, $user->getRoles()->getArrayOfPrimitives())) {
            return Role::STUDENT;
        }
        if (in_array(Role::TEACHER, $user->getRoles()->getArrayOfPrimitives())) {
            return Role::TEACHER;
        }
        throw new RoleIsNotStudentOrTeacherException();
    }

    protected function ensureRolesAreNotEqual(User $firstUser, User $secondUser): void
    {
        if ($this->verifyRole($firstUser) === $this->verifyRole($secondUser)) {
            throw new RolesOfUsersEqualException();
        }
    }

    protected function identifyUserById(string $id): User
    {
        $user = $this->userRepository->ofId($this->createIdFromPrimitive($id));
        if (null === $user) {
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

    protected function ensureConnectionsExist(?array $connections): void
    {
        if (true === empty($connections)) {
            throw new ConnectionNotFound();
        }
    }

    protected function verifySpecifierChanged(Uuid $newSpecifier, Uuid $oldSpecifier)
    {
        return $oldSpecifier->toString() !== $newSpecifier->toString();
    }

    protected function recognizeSpecifier($authorId, $firstUserId, $secondUserId): Uuid
    {
        if ($authorId === $firstUserId) {
            return $this->createIdFromPrimitive($firstUserId);
        }
        if ($authorId === $secondUserId) {
            return $this->createIdFromPrimitive($secondUserId);
        }
        throw new PermissionDeniedException();
    }

    protected function createFiltersByUserId(User $user): array
    {
        if (Role::STUDENT === $this->verifyRole($user)) {
            return [['field' => 'studentId', 'operator' => '=', 'value' => $user->getId()->toString()]];
        }
        return [['field' => 'teacherId', 'operator' => '=', 'value' => $user->getId()->toString()]];
    }
}