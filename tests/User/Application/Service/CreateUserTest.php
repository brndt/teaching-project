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
        $this->repository->method('searchByEmail')->willReturn($this->createRandomUser());
        $this->expectException(UserAlreadyExistsException::class);
        ($this->createUser)($this->createRandomUserRequest());
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentValidationBecauseOfEmailException()
    {
        $this->expectException(InvalidArgumentValidationException::class);
        ($this->createUser)($this->createRandomUserWithInvalidEmailRequest());
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentValidationBecauseOfRoleException()
    {
        $this->expectException(InvalidArgumentValidationException::class);
        ($this->createUser)($this->createRandomUserWithInvalidRoleRequest());
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentValidationBecauseOfInvalidPasswordLengthException()
    {
        $this->expectException(InvalidArgumentValidationException::class);
        ($this->createUser)($this->createRandomUserWithInvalidPasswordLengthRequest());
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentValidationBecauseOfInvalidNumberContainingException()
    {
        $this->expectException(InvalidArgumentValidationException::class);
        ($this->createUser)($this->createRandomUserWithInvalidNumberContainingRequest());
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentValidationBecauseOfInvalidLetterContainingException()
    {
        $this->expectException(InvalidArgumentValidationException::class);
        ($this->createUser)($this->createRandomUserWithInvalidLetterContainingRequest());
    }

    /**
     * @test
     */
    public function shouldSaveUserBecauseDoesntExist()
    {
        $this->repository->method('searchByEmail')->willReturn(null);
        $this->assertNull(($this->createUser)($this->createRandomUserRequest()));
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

    private function createRandomUserRequest(): CreateUserRequest
    {
        return new CreateUserRequest(
            'user@example.com',
            '123456aa',
            'Alex',
            'Johnson',
            ['teacher']
        );
    }

    private function createRandomUserWithInvalidEmailRequest(): CreateUserRequest
    {
        return new CreateUserRequest(
            'userexample.com',
            '123456aa',
            'Alex',
            'Johnson',
            ['teacher']
        );
    }

    private function createRandomUserWithInvalidRoleRequest(): CreateUserRequest
    {
        return new CreateUserRequest(
            'usere@xample.com',
            '123456aa',
            'Alex',
            'Johnson',
            ['something_invalid']
        );
    }

    private function createRandomUserWithInvalidPasswordLengthRequest(): CreateUserRequest
    {
        return new CreateUserRequest(
            'usere@xample.com',
            '123456a',
            'Alex',
            'Johnson',
            ['teacher']
        );
    }

    private function createRandomUserWithInvalidNumberContainingRequest(): CreateUserRequest
    {
        return new CreateUserRequest(
            'usere@xample.com',
            'qwertyuiop',
            'Alex',
            'Johnson',
            ['teacher']
        );
    }

    private function createRandomUserWithInvalidLetterContainingRequest(): CreateUserRequest
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