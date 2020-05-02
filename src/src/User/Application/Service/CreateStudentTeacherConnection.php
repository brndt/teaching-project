<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\ConnectionAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Exception\RoleIsNotStudentOrTeacherException;
use LaSalle\StudentTeacher\User\Application\Exception\RolesOfUsersEqualException;
use LaSalle\StudentTeacher\User\Application\Exception\UserAreEqualException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\CreateStudentTeacherConnectionRequest;
use LaSalle\StudentTeacher\User\Domain\Aggregate\StudentTeacherConnection;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\StudentTeacherConnectionRepository;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\RequestStatus;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;

final class CreateStudentTeacherConnection
{
    private UserRepository $userRepository;
    private StudentTeacherConnectionRepository $studentTeacherConnectionRepository;

    public function __construct(
        UserRepository $userRepository,
        StudentTeacherConnectionRepository $studentTeacherConnectionRepository
    ) {
        $this->userRepository = $userRepository;
        $this->studentTeacherConnectionRepository = $studentTeacherConnectionRepository;
    }

    public function __invoke(CreateStudentTeacherConnectionRequest $request)
    {
        $requestingUser = $this->identifyUserById($request->getRequestingId());
        $pendingUser = $this->identifyUserById($request->getPendingId());

        [$student, $teacher] = $this->identifyRoles($requestingUser, $pendingUser);

        $this->ensureConnectionIsNotAlreadyExists($student, $teacher);

        $studentTeacherConnection = new StudentTeacherConnection(
            $student->getId(),
            $teacher->getId(),
            new RequestStatus(RequestStatus::STATUS_PENDING)
        );

        $this->studentTeacherConnectionRepository->save($studentTeacherConnection);
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

    private function identifyRoles(User $requestingUser, User $pendingUser): array
    {
        if ($this->verifyRole($requestingUser) === $this->verifyRole($pendingUser)) {
            throw new RolesOfUsersEqualException();
        }
        if ($requestingUser->getId()->toString() === $pendingUser->getId()->toString()) {
            throw new UserAreEqualException();
        }
        return $this->verifyRole($requestingUser) === Role::STUDENT ? [$requestingUser, $pendingUser] : [$pendingUser, $requestingUser];
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
        if (null !== $this->studentTeacherConnectionRepository->ofId($student->getId(), $teacher->getId())) {
            throw new ConnectionAlreadyExistsException();
        }
    }
}