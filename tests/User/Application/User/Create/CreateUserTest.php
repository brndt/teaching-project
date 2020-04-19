<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\User\Create;

use LaSalle\StudentTeacher\Shared\Domain\DomainEventBus;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\User\Create\CreateUser;
use LaSalle\StudentTeacher\User\Application\User\Create\CreateUserRequest;
use LaSalle\StudentTeacher\User\Domain\Event\UserCreatedDomainEvent;
use LaSalle\StudentTeacher\User\Domain\PasswordHashing;
use LaSalle\StudentTeacher\User\Domain\UserRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\Infrastructure\EventBus\InMemoryDomainEventBus;
use Test\LaSalle\StudentTeacher\User\Infrastructure\EventBus\InMemoryDomainEventSubscriber;
use Test\LaSalle\StudentTeacher\User\Infrastructure\Persistence\InMemoryUserRepository;

final class CreateUserTest extends TestCase
{
    private CreateUser $createUser;
    private UserRepository $repository;
    private MockObject $passwordHashing;
    private DomainEventBus $eventBus;

    public function setUp(): void
    {
        $this->repository = new InMemoryUserRepository();
        $this->passwordHashing = $this->createMock(PasswordHashing::class);
        $this->eventBus = new InMemoryDomainEventBus();
        $this->createUser = new CreateUser($this->repository, $this->passwordHashing, $this->eventBus);
    }

    private function executeCreateUser()
    {
        return ($this->createUser)(
            new CreateUserRequest(
                'user@example.com',
                'uuidgenerated',
                '123456Aaa',
                'Alex',
                'Johnson',
                ['ROLE_STUDENT']
            )
        );
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

    /**
     * @test
     */
    public function afterUserSignUpItShouldBeInTheRepository()
    {
        $this->executeCreateUser();

        $this->assertNotNull(
            $this->repository->searchByEmail('user@example.com')
        );
    }

    /**
     * @test
     */
    public function itShouldPublishUserCreatedEvent()
    {
        $subscriber = new InMemoryDomainEventSubscriber();
        $this->eventBus->subscribe($subscriber);

        $this->executeCreateUser();
        $this->assertInstanceOf(UserCreatedDomainEvent::class, $subscriber->event);
        $this->assertNotNull($subscriber->event->getId());
    }
}