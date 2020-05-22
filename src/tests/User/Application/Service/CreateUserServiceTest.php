<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use DateTimeImmutable;
use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEventBus;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Request\CreateUserRequest;
use LaSalle\StudentTeacher\User\Application\Service\CreateUserService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\UserBuilder;

final class CreateUserServiceTest extends TestCase
{
    private CreateUserService $createUser;
    private MockObject $repository;
    private MockObject $eventBus;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->eventBus = $this->createMock(DomainEventBus::class);
        $this->createUser = new CreateUserService($this->repository, $this->eventBus);
    }

    public function testWhenUserEmailIsInvalidThenThrowException()
    {
        $request = new CreateUserRequest(
            'userexample.com', '123456aa', 'Alex', 'Johnson', ['teacher'], new \DateTimeImmutable('2020-04-27')
        );

        $this->expectException(InvalidArgumentException::class);
        ($this->createUser)($request);
    }

    public function testWhenUserAlreadyExistsThenThrowException()
    {
        $request = new CreateUserRequest(
            'user@example.com', '123456aa', 'Alex', 'Johnson', ['teacher'], new \DateTimeImmutable('2020-04-27')
        );
        $user = (new UserBuilder())->build();

        $this->repository->method('ofEmail')->willReturn($user);
        $this->expectException(UserAlreadyExistsException::class);
        ($this->createUser)($request);
    }

    public function testWhenUserPasswordIsInvalidThenThrowException()
    {
        $request = new CreateUserRequest(
            'user@example.com', '123456a', 'Alex', 'Johnson', ['teacher'], new \DateTimeImmutable('2020-04-27')
        );

        $this->expectException(InvalidArgumentException::class);
        ($this->createUser)($request);
    }

    public function testWhenPasswordDoesntContainNumberThenThrowException()
    {
        $request = new CreateUserRequest(
            'user@example.com',
            'qwertyuiop',
            'Alex',
            'Johnson',
            ['teacher'],
            new \DateTimeImmutable('2020-04-27')
        );

        $this->expectException(InvalidArgumentException::class);
        ($this->createUser)($request);
    }

    public function testWhenPasswordDoesntContainLetterThenThrowException()
    {
        $request = new CreateUserRequest(
            'user@example.com',
            '123456789',
            'Alex',
            'Johnson',
            ['teacher'],
            new \DateTimeImmutable()
        );

        $this->expectException(InvalidArgumentException::class);
        ($this->createUser)($request);
    }

    public function testWhenFirstNameIsInvalidThenThrowException()
    {
        $request = new CreateUserRequest(
            'user@example.com', '123456aa', 'Alex.', 'Johnson', ['teacher'], new DateTimeImmutable()
        );

        $this->expectException(InvalidArgumentException::class);
        ($this->createUser)($request);
    }

    public function testWhenLastNameIsInvalidThenThrowException()
    {
        $request = new CreateUserRequest(
            'user@example.com', '123456aa', 'Alex', 'Johnson ', ['teacher'], new \DateTimeImmutable()
        );

        $this->expectException(InvalidArgumentException::class);
        ($this->createUser)($request);
    }

    public function testWhenUserRoleIsInvalidThenThrowException()
    {
        $request = new CreateUserRequest(
            'user@example.com', '123456aa', 'Alex', 'Johnson', ['invalid'], new DateTimeImmutable('2020-04-27')
        );

        $this->expectException(InvalidArgumentException::class);
        ($this->createUser)($request);
    }

    public function testWhenUserRoleIsAdminThenThrowException()
    {
        $request = new CreateUserRequest(
            'user@example.com', '123456aa', 'Alex', 'Johnson', ['admin'], new DateTimeImmutable('2020-04-27')
        );

        $this->expectException(PermissionDeniedException::class);
        ($this->createUser)($request);
    }

    public function testWhenRequestIsValidThenCreateUser()
    {
        $request = new CreateUserRequest(
            'user@example.com', '123456aa', 'Alex', 'Johnson', ['teacher'], new DateTimeImmutable()
        );
        $user = (new UserBuilder())
            ->withEmail(new Email($request->getEmail()))
            ->withPassword(Password::fromPlainPassword($request->getPassword()))
            ->withFirstName(new Name($request->getFirstName()))
            ->withLastName(new Name($request->getLastName()))
            ->withRoles(Roles::fromArrayOfPrimitives($request->getRoles()))
            ->withCreated($request->getCreated())
            ->build();

        $this->repository->expects($this->once())->method('ofEmail')->willReturn(null);
        $this->repository->expects($this->once())->method('save')->with(
            $this->callback($this->userComparator($user, $request->getPassword()))
        );
        $this->eventBus->expects($this->atLeastOnce())->method('dispatch');
        ($this->createUser)($request);
    }

    public function userComparator(User $userExpected, string $plainPassword): callable
    {
        return function (User $userActual) use ($userExpected, $plainPassword) {
            return
                $userExpected->getEmail()->toString() === $userActual->getEmail()->toString()
                && password_verify($plainPassword, $userActual->getPassword()->toString())
                && $userExpected->getFirstName()->toString() === $userActual->getFirstName()->toString()
                && $userExpected->getLastName()->toString() === $userActual->getLastName()->toString()
                && $userExpected->getRoles()->getArrayOfPrimitives() === $userActual->getRoles()->getArrayOfPrimitives()
                && $userExpected->getCreated()->diff($userActual->getCreated()) -> s < 10;
        };
    }
}