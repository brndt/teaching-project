<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEventBus;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Request\CreateUserRequest;
use LaSalle\StudentTeacher\User\Application\Service\CreateUser;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateUserTest extends TestCase
{
    private CreateUser $createUser;
    private MockObject $repository;
    private MockObject $eventBus;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->eventBus = $this->createMock(DomainEventBus::class);
        $this->createUser = new CreateUser($this->repository, $this->eventBus);
    }

    /**
     * @test
     */
    public function shouldThrowUserAlreadyExistsException()
    {
        $this->repository->method('ofEmail')->willReturn($this->anyValidUser());
        $this->expectException(UserAlreadyExistsException::class);
        ($this->createUser)($this->anyValidUserRequest());
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentValidationExceptionBecauseOfEmail()
    {
        $this->expectException(InvalidArgumentValidationException::class);
        ($this->createUser)($this->anyUserRequestWithInvalidEmail());
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentValidationExceptionBecauseOfRole()
    {
        $this->expectException(InvalidArgumentValidationException::class);
        ($this->createUser)($this->anyUserRequestWithInvalidRole());
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentValidationExceptionBecauseOfInvalidPasswordLength()
    {
        $this->expectException(InvalidArgumentValidationException::class);
        ($this->createUser)($this->anyUserRequestWithInvalidPasswordLength());
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentValidationExceptionBecauseOfInvalidNumberContaining()
    {
        $this->expectException(InvalidArgumentValidationException::class);
        ($this->createUser)($this->anyUserRequestWithInvalidNumberContaining());
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentValidationExceptionBecauseOfInvalidLetterContaining()
    {
        $this->expectException(InvalidArgumentValidationException::class);
        ($this->createUser)($this->anyUserRequestWithInvalidLetterContaining());
    }

    /**
     * @test
     */
    public function shouldSaveUser()
    {
        $this->repository->method('nextIdentity')->willReturn(Uuid::fromString('16bf6c6a-c855-4a36-a3dd-5b9f6d92c753'));

        $this->repository->expects($this->once())->method('save')->with($this->anyValidUser());

        ($this->createUser)($this->anyValidUserRequest());
    }

    /**
     * @test
     */
    public function shouldDispatchDomainEvent()
    {
        $this->eventBus->expects($this->atLeastOnce())->method('dispatch');
        ($this->createUser)($this->anyValidUserRequest());
    }

    private function anyValidUser(): User
    {
        return new User(
            Uuid::fromString('16bf6c6a-c855-4a36-a3dd-5b9f6d92c753'),
            new Email('user@example.com'),
            Password::fromPlainPassword('123456aa'),
            'Alex',
            'Johnson',
            Roles::fromArrayOfPrimitives(['teacher']),
            new \DateTimeImmutable('2020-04-27')
        );
    }

    private function anyValidUserRequest(): CreateUserRequest
    {
        return new CreateUserRequest(
            'user@example.com',
            '123456aa',
            'Alex',
            'Johnson',
            ['teacher'],
            new \DateTimeImmutable('2020-04-27')
        );
    }

    private function anyUserRequestWithInvalidEmail(): CreateUserRequest
    {
        return new CreateUserRequest(
            'userexample.com',
            '123456aa',
            'Alex',
            'Johnson',
            ['teacher'],
            new \DateTimeImmutable('2020-04-27')
        );
    }

    private function anyUserRequestWithInvalidRole(): CreateUserRequest
    {
        return new CreateUserRequest(
            'user@example.com',
            '123456aa',
            'Alex',
            'Johnson',
            ['something_invalid'],
            new \DateTimeImmutable('2020-04-27')
        );
    }

    private function anyUserRequestWithInvalidPasswordLength(): CreateUserRequest
    {
        return new CreateUserRequest(
            'user@example.com',
            '123456a',
            'Alex',
            'Johnson',
            ['teacher'],
            new \DateTimeImmutable('2020-04-27')
        );
    }

    private function anyUserRequestWithInvalidNumberContaining(): CreateUserRequest
    {
        return new CreateUserRequest(
            'user@example.com',
            'qwertyuiop',
            'Alex',
            'Johnson',
            ['teacher'],
            new \DateTimeImmutable('2020-04-27')
        );
    }

    private function anyUserRequestWithInvalidLetterContaining(): CreateUserRequest
    {
        return new CreateUserRequest(
            'user@example.com',
            '123456789',
            'Alex',
            'Johnson',
            ['teacher'],
            new \DateTimeImmutable()
        );
    }
}