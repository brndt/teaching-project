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
    public function userAlreadyExistsShouldThrowAnException()
    {
        $this->repository->method('ofEmail')->willReturn($this->createRandomUser());
        $this->expectException(UserAlreadyExistsException::class);
        ($this->createUser)($this->anyUserRequest());
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentValidationBecauseOfEmailException()
    {
        $this->expectException(InvalidArgumentValidationException::class);
        ($this->createUser)($this->anyUserWithInvalidEmailRequest());
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentValidationBecauseOfRoleException()
    {
        $this->expectException(InvalidArgumentValidationException::class);
        ($this->createUser)($this->anyUserWithInvalidRoleRequest());
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentValidationBecauseOfInvalidPasswordLengthException()
    {
        $this->expectException(InvalidArgumentValidationException::class);
        ($this->createUser)($this->anyUserWithInvalidPasswordLengthRequest());
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentValidationBecauseOfInvalidNumberContainingException()
    {
        $this->expectException(InvalidArgumentValidationException::class);
        ($this->createUser)($this->anyUserWithInvalidNumberContainingRequest());
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentValidationBecauseOfInvalidLetterContainingException()
    {
        $this->expectException(InvalidArgumentValidationException::class);
        ($this->createUser)($this->anyUserWithInvalidLetterContainingRequest());
    }

    /**
     * @test
     */
    public function shouldSaveUserBecauseDoesntExist()
    {
        $this->assertNull(($this->createUser)($this->anyUserRequest()));
    }

    private function createRandomUser(): User
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

    private function anyUserRequest(): CreateUserRequest
    {
        return new CreateUserRequest(
            'user@example.com',
            '123456aa',
            'Alex',
            'Johnson',
            ['teacher']
        );
    }

    private function anyUserWithInvalidEmailRequest(): CreateUserRequest
    {
        return new CreateUserRequest(
            'userexample.com',
            '123456aa',
            'Alex',
            'Johnson',
            ['teacher']
        );
    }

    private function anyUserWithInvalidRoleRequest(): CreateUserRequest
    {
        return new CreateUserRequest(
            'usere@xample.com',
            '123456aa',
            'Alex',
            'Johnson',
            ['something_invalid']
        );
    }

    private function anyUserWithInvalidPasswordLengthRequest(): CreateUserRequest
    {
        return new CreateUserRequest(
            'usere@xample.com',
            '123456a',
            'Alex',
            'Johnson',
            ['teacher']
        );
    }

    private function anyUserWithInvalidNumberContainingRequest(): CreateUserRequest
    {
        return new CreateUserRequest(
            'usere@xample.com',
            'qwertyuiop',
            'Alex',
            'Johnson',
            ['teacher']
        );
    }

    private function anyUserWithInvalidLetterContainingRequest(): CreateUserRequest
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