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
        $this->assertNull(($this->createUser)($this->anyValidUserRequest()));
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
            Uuid::generate(),
            new Email('hola@mundo.com'),
            Password::fromPlainPassword('123456aa'),
            'alex',
            'johnson',
            Roles::fromArrayOfPrimitives(['teacher']),
            new \DateTimeImmutable()
        );
    }

    private function anyValidUserRequest(): CreateUserRequest
    {
        return new CreateUserRequest(
            'user@example.com',
            '123456aa',
            'Alex',
            'Johnson',
            ['teacher']
        );
    }

    private function anyUserRequestWithInvalidEmail(): CreateUserRequest
    {
        return new CreateUserRequest(
            'userexample.com',
            '123456aa',
            'Alex',
            'Johnson',
            ['teacher']
        );
    }

    private function anyUserRequestWithInvalidRole(): CreateUserRequest
    {
        return new CreateUserRequest(
            'usere@xample.com',
            '123456aa',
            'Alex',
            'Johnson',
            ['something_invalid']
        );
    }

    private function anyUserRequestWithInvalidPasswordLength(): CreateUserRequest
    {
        return new CreateUserRequest(
            'usere@xample.com',
            '123456a',
            'Alex',
            'Johnson',
            ['teacher']
        );
    }

    private function anyUserRequestWithInvalidNumberContaining(): CreateUserRequest
    {
        return new CreateUserRequest(
            'usere@xample.com',
            'qwertyuiop',
            'Alex',
            'Johnson',
            ['teacher']
        );
    }

    private function anyUserRequestWithInvalidLetterContaining(): CreateUserRequest
    {
        return new CreateUserRequest(
            'usere@xample.com',
            '123456789',
            'Alex',
            'Johnson',
            ['teacher']
        );
    }
}