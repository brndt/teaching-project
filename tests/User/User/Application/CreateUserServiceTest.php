<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\User\Application;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEventBus;
use LaSalle\StudentTeacher\Shared\Domain\RandomStringGenerator;
use LaSalle\StudentTeacher\User\User\Application\Exception\EmailAlreadyExistsException;
use LaSalle\StudentTeacher\User\Shared\Application\Request\CreateUserRequest;
use LaSalle\StudentTeacher\User\User\Application\Service\CreateUserService;
use LaSalle\StudentTeacher\User\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\User\Domain\Exception\InvalidEmailException;
use LaSalle\StudentTeacher\User\User\Domain\Exception\InvalidLetterContainingException;
use LaSalle\StudentTeacher\User\User\Domain\Exception\InvalidNameException;
use LaSalle\StudentTeacher\User\User\Domain\Exception\InvalidNumberContainingException;
use LaSalle\StudentTeacher\User\User\Domain\Exception\InvalidPasswordLengthException;
use LaSalle\StudentTeacher\User\User\Domain\Exception\InvalidRoleException;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Name;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\User\Domain\UserBuilder;

final class CreateUserServiceTest extends TestCase
{
    private CreateUserService $createUser;
    private MockObject $repository;
    private MockObject $randomStringGenerator;
    private MockObject $eventBus;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->eventBus = $this->createMock(DomainEventBus::class);
        $this->randomStringGenerator = $this->createMock(RandomStringGenerator::class);
        $authorizationService = $this->createMock(AuthorizationService::class);

        $this->createUser = new CreateUserService($this->randomStringGenerator, $this->repository, $this->eventBus, $authorizationService);
    }

    public function testWhenUserEmailIsInvalidThenThrowException()
    {
        $this->expectException(InvalidEmailException::class);

        $request = new CreateUserRequest(
            'userexample.com', '123456aa', 'Alex', 'Johnson', ['teacher'], new \DateTimeImmutable('2020-04-27')
        );

        ($this->createUser)($request);
    }

    public function testWhenUserEmailAlreadyExistsThenThrowException()
    {
        $request = new CreateUserRequest(
            'user@example.com', '123456aa', 'Alex', 'Johnson', ['teacher'], new \DateTimeImmutable('2020-04-27')
        );
        $user = (new UserBuilder())->build();

        $this->repository->method('ofEmail')->willReturn($user);
        $this->expectException(EmailAlreadyExistsException::class);
        ($this->createUser)($request);
    }

    public function testWhenUserPasswordIsInvalidThenThrowException()
    {
        $request = new CreateUserRequest(
            'user@example.com', '123456a', 'Alex', 'Johnson', ['teacher'], new \DateTimeImmutable('2020-04-27')
        );

        $this->expectException(InvalidPasswordLengthException::class);
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

        $this->expectException(InvalidNumberContainingException::class);
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

        $this->expectException(InvalidLetterContainingException::class);
        ($this->createUser)($request);
    }

    public function testWhenFirstNameIsInvalidThenThrowException()
    {
        $this->expectException(InvalidNameException::class);

        $request = new CreateUserRequest(
            'user@example.com', '123456aa', 'Alex.', 'Johnson', ['teacher'], new DateTimeImmutable()
        );

        ($this->createUser)($request);
    }

    public function testWhenLastNameIsInvalidThenThrowException()
    {
        $request = new CreateUserRequest(
            'user@example.com', '123456aa', 'Alex', 'Johnson ', ['teacher'], new \DateTimeImmutable()
        );

        $this->expectException(InvalidNameException::class);
        ($this->createUser)($request);
    }

    public function testWhenUserRoleIsInvalidThenThrowException()
    {
        $request = new CreateUserRequest(
            'user@example.com', '123456aa', 'Alex', 'Johnson', ['invalid'], new DateTimeImmutable('2020-04-27')
        );

        $this->expectException(InvalidRoleException::class);
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

        $this->repository->expects(self::once())->method('ofEmail')->willReturn(null);
        $this->repository->expects(self::once())->method('save')->with(
            $this->callback($this->userComparator($user, $request->getPassword()))
        );

        $this->eventBus
            ->expects(self::atLeastOnce())
            ->method('dispatch');

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
                && $userExpected->getCreated()->diff($userActual->getCreated())->s < 10;
        };
    }
}
