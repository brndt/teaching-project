<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Create;

use LaSalle\StudentTeacher\Shared\Domain\DomainEventBus;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\User\Create\CreateUser;
use LaSalle\StudentTeacher\User\Application\User\Create\CreateUserRequest;
use LaSalle\StudentTeacher\User\Domain\PasswordHashing;
use LaSalle\StudentTeacher\User\Domain\UserRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class CreateUserTest extends TestCase
{
    private CreateUser $createUser;
    private MockObject $passwordHashing;
    private MockObject $repository;
    private MockObject $eventBus;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->passwordHashing = $this->createMock(PasswordHashing::class);
        $this->eventBus = $this->createMock(DomainEventBus::class);
        $this->createUser = new CreateUser($this->repository, $this->passwordHashing, $this->eventBus);
    }

    /**
     * @test
     */
    public function userAlreadyExistsShouldThrowAnException()
    {
        $this->expectException(UserAlreadyExistsException::class);
        $this->executeCreateUser();
        $this->executeCreateUser();
    }

    private function executeCreateUser()
    {
        return ($this->createUser)(
            new CreateUserRequest(
                'user@example.com',
                Uuid::uuid4()->toString(),
                '123456Aaa',
                'Alex',
                'Johnson',
                ['ROLE_STUDENT']
            )
        );
    }
}