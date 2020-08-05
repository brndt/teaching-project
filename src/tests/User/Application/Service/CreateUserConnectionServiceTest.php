<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\ConnectionAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Exception\RolesOfUsersEqualException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Exception\UsersAreEqualException;
use LaSalle\StudentTeacher\User\Application\Request\CreateUserConnectionRequest;
use LaSalle\StudentTeacher\User\Application\Service\CreateUserConnectionService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\UserConnection;
use LaSalle\StudentTeacher\User\Domain\Repository\UserConnectionRepository;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use LaSalle\StudentTeacher\User\Domain\ValueObject\State\Pended;
use LaSalle\StudentTeacher\User\Domain\ValueObject\State\StateFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class CreateUserConnectionServiceTest extends TestCase
{
    private CreateUserConnectionService $createUserConnectionService;
    protected MockObject $userConnectionRepository;
    protected MockObject $userRepository;
    protected MockObject $stateFactory;

    public function setUp(): void
    {
        $this->userConnectionRepository = $this->createMock(UserConnectionRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->stateFactory = $this->createMock(StateFactory::class);
        $this->createUserConnectionService = new CreateUserConnectionService(
            $this->userConnectionRepository,
            $this->userRepository,
            $this->stateFactory
        );
    }

    public function testWhenRequestAuthorIsInvalidThenThrowException()
    {
        $request = new CreateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138-invalid',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138'
        );

        $this->expectException(InvalidArgumentException::class);
        ($this->createUserConnectionService)($request);
    }

    public function testWhenRequestAuthorIsNotFoundThenThrowException()
    {
        $request = new CreateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138'
        );

        $this->expectException(UserNotFoundException::class);
        $this->userRepository->expects($this->once())->method('ofId')->with($request->getRequestAuthorId())->willReturn(
            null
        );
        ($this->createUserConnectionService)($request);
    }

    public function testWhenFirstUserIdIsInvalidThenThrowException()
    {
        $request = new CreateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794-invalid',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138'
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $this->expectException(InvalidArgumentException::class);
        $this->userRepository->expects($this->at(0))->method('ofId')->with(
            $request->getRequestAuthorId()
        )->willReturn($author);
        ($this->createUserConnectionService)($request);
    }

    public function testWhenFirstUserIsNotFoundThenThrowException()
    {
        $request = new CreateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138'
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $this->expectException(UserNotFoundException::class);
        $this->userRepository->expects($this->at(0))->method('ofId')->with(
            $request->getRequestAuthorId()
        )->willReturn($author);
        $this->userRepository->expects($this->at(1))->method('ofId')->with(
            $request->getFirstUser()
        )->willReturn(null);
        ($this->createUserConnectionService)($request);
    }

    public function testWhenSecondUserIdIsInvalidThenThrowException()
    {
        $request = new CreateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138-invalid'
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $firstUser = (new UserBuilder())
            ->withId(new Uuid($request->getFirstUser()))
            ->build();

        $this->expectException(InvalidArgumentException::class);
        $this->userRepository->expects($this->at(0))->method('ofId')->with(
            $request->getRequestAuthorId()
        )->willReturn($author);
        $this->userRepository->expects($this->at(1))->method('ofId')->with(
            $request->getFirstUser()
        )->willReturn($firstUser);
        ($this->createUserConnectionService)($request);
    }

    public function testWhenSecondUserIsNotFoundThenThrowException()
    {
        $request = new CreateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753'
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $firstUser = (new UserBuilder())
            ->withId(new Uuid($request->getFirstUser()))
            ->build();

        $this->expectException(UserNotFoundException::class);
        $this->userRepository->expects($this->at(0))->method('ofId')->with(
            $request->getRequestAuthorId()
        )->willReturn($author);
        $this->userRepository->expects($this->at(1))->method('ofId')->with(
            $request->getFirstUser()
        )->willReturn($firstUser);
        $this->userRepository->expects($this->at(2))->method('ofId')->with(
            $request->getSecondUser()
        )->willReturn(null);
        ($this->createUserConnectionService)($request);
    }

    public function testWhenUsersAreEqualThenThrowException()
    {
        $request = new CreateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138'
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $firstUser = (new UserBuilder())
            ->withId(new Uuid($request->getFirstUser()))
            ->build();
        $secondUser = (new UserBuilder())
            ->withId(new Uuid($request->getSecondUser()))
            ->build();

        $this->expectException(UsersAreEqualException::class);
        $this->userRepository->expects($this->at(0))->method('ofId')->with(
            $request->getRequestAuthorId()
        )->willReturn($author);
        $this->userRepository->expects($this->at(1))->method('ofId')->with(
            $request->getFirstUser()
        )->willReturn($firstUser);
        $this->userRepository->expects($this->at(2))->method('ofId')->with(
            $request->getSecondUser()
        )->willReturn($secondUser);
        ($this->createUserConnectionService)($request);
    }

    public function testWhenUsersRolesAreEqualThenThrowException()
    {
        $request = new CreateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138'
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $firstUser = (new UserBuilder())
            ->withId(new Uuid($request->getFirstUser()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::TEACHER]))
            ->build();
        $secondUser = (new UserBuilder())
            ->withId(new Uuid($request->getSecondUser()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::TEACHER]))
            ->build();

        $this->expectException(RolesOfUsersEqualException::class);
        $this->userRepository->expects($this->at(0))->method('ofId')->with(
            $request->getRequestAuthorId()
        )->willReturn($author);
        $this->userRepository->expects($this->at(1))->method('ofId')->with(
            $request->getFirstUser()
        )->willReturn($firstUser);
        $this->userRepository->expects($this->at(2))->method('ofId')->with(
            $request->getSecondUser()
        )->willReturn($secondUser);
        ($this->createUserConnectionService)($request);
    }

    public function testWhenUsersRolesAreNotStudentOrTeacherThenThrowException()
    {
        $request = new CreateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138'
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $firstUser = (new UserBuilder())
            ->withId(new Uuid($request->getFirstUser()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::ADMIN]))
            ->build();
        $secondUser = (new UserBuilder())
            ->withId(new Uuid($request->getSecondUser()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::TEACHER]))
            ->build();

        $this->expectException(PermissionDeniedException::class);
        $this->userRepository->expects($this->at(0))->method('ofId')->with(
            $request->getRequestAuthorId()
        )->willReturn($author);
        $this->userRepository->expects($this->at(1))->method('ofId')->with(
            $request->getFirstUser()
        )->willReturn($firstUser);
        $this->userRepository->expects($this->at(2))->method('ofId')->with(
            $request->getSecondUser()
        )->willReturn($secondUser);
        ($this->createUserConnectionService)($request);
    }

    public function testWhenConnectionAlreadyExistsThenThrowException()
    {
        $request = new CreateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138'
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $firstUser = (new UserBuilder())
            ->withId(new Uuid($request->getFirstUser()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::STUDENT]))
            ->build();
        $secondUser = (new UserBuilder())
            ->withId(new Uuid($request->getSecondUser()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::TEACHER]))
            ->build();

        $userConnection = new UserConnection($firstUser->getId(), $secondUser->getId(), new Pended(), $author->getId());

        $this->expectException(ConnectionAlreadyExistsException::class);
        $this->userRepository->expects($this->at(0))->method('ofId')->with(
            $request->getRequestAuthorId()
        )->willReturn($author);
        $this->userRepository->expects($this->at(1))->method('ofId')->with(
            $request->getFirstUser()
        )->willReturn($firstUser);
        $this->userRepository->expects($this->at(2))->method('ofId')->with(
            $request->getSecondUser()
        )->willReturn($secondUser);
        $this->userConnectionRepository->expects($this->once())->method('ofId')->willReturn($userConnection);

        ($this->createUserConnectionService)($request);
    }

    public function testWhenRequestIsValidThenCreateUserConnection()
    {
        $request = new CreateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138'
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $firstUser = (new UserBuilder())
            ->withId(new Uuid($request->getFirstUser()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::STUDENT]))
            ->build();
        $secondUser = (new UserBuilder())
            ->withId(new Uuid($request->getSecondUser()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::TEACHER]))
            ->build();

        $userConnection = new UserConnection($firstUser->getId(), $secondUser->getId(), new Pended(), $author->getId());

        $this->userRepository->expects($this->at(0))->method('ofId')->with(
            $request->getRequestAuthorId()
        )->willReturn($author);
        $this->userRepository->expects($this->at(1))->method('ofId')->with(
            $request->getFirstUser()
        )->willReturn($firstUser);
        $this->userRepository->expects($this->at(2))->method('ofId')->with(
            $request->getSecondUser()
        )->willReturn($secondUser);
        $this->userConnectionRepository->expects($this->once())->method('ofId')->willReturn(null);
        $this->userConnectionRepository->expects($this->once())->method('save')->with($userConnection);

        ($this->createUserConnectionService)($request);
    }
}