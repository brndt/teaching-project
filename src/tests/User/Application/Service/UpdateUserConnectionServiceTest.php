<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\ConnectionNotFoundException;
use LaSalle\StudentTeacher\User\Application\Exception\RolesOfUsersEqualException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Exception\UsersAreEqualException;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserConnectionRequest;
use LaSalle\StudentTeacher\User\Application\Service\UpdateUserConnectionService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\UserConnection;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidStateException;
use LaSalle\StudentTeacher\User\Domain\Repository\UserConnectionRepository;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use LaSalle\StudentTeacher\User\Domain\ValueObject\State\Approved;
use LaSalle\StudentTeacher\User\Domain\ValueObject\State\Pended;
use LaSalle\StudentTeacher\User\Domain\ValueObject\State\StateFactory;
use LaSalle\StudentTeacher\User\Domain\ValueObject\State\Withdrawn;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class UpdateUserConnectionServiceTest extends TestCase
{
    private UpdateUserConnectionService $updateUserConnectionService;
    protected MockObject $userConnectionRepository;
    protected MockObject $userRepository;
    protected MockObject $stateFactory;

    public function setUp(): void
    {
        $this->userConnectionRepository = $this->createMock(UserConnectionRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->stateFactory = $this->createMock(StateFactory::class);
        $coursePermissionRepository = $this->createMock(CoursePermissionRepository::class);
        $unitRepository = $this->createMock(UnitRepository::class);
        $authorizationService = new AuthorizationService($coursePermissionRepository, $unitRepository);

        $this->updateUserConnectionService = new UpdateUserConnectionService(
            $this->userConnectionRepository,
            $this->userRepository,
            $this->stateFactory,
            $authorizationService
        );
    }

    public function testWhenRequestAuthorIdIsInvalidThenThrowException()
    {
        $this->expectException(InvalidUuidException::class);

        $request = new UpdateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138-invalid',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'approved'
        );

        ($this->updateUserConnectionService)($request);
    }

    public function testWhenRequestAuthorIsNotFoundThenThrowException()
    {
        $request = new UpdateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'approved'
        );

        $this->expectException(UserNotFoundException::class);
        $this->userRepository
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn(null);

        ($this->updateUserConnectionService)($request);
    }

    public function testWhenFirstUserIdIsInvalidThenThrowException()
    {
        $this->expectException(InvalidUuidException::class);

        $request = new UpdateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794-invalid',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'approved'
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $this->userRepository
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        ($this->updateUserConnectionService)($request);
    }

    public function testWhenFirstUserIsNotFoundThenThrowException()
    {
        $request = new UpdateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'approved'
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $this->expectException(UserNotFoundException::class);

        $this->userRepository
            ->expects($this->at(0))
            ->method('ofId')
            ->with(new Uuid($request->getRequestAuthorId()))
            ->willReturn($author);

        $this->userRepository
            ->expects($this->at(1))
            ->method('ofId')
            ->with(new Uuid($request->getFirstUser()))
            ->willReturn(null);

        ($this->updateUserConnectionService)($request);
    }

    public function testWhenSecondUserIdIsInvalidThenThrowException()
    {
        $this->expectException(InvalidUuidException::class);

        $request = new UpdateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138-invalid',
            'approved'
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $firstUser = (new UserBuilder())
            ->withId(new Uuid($request->getFirstUser()))
            ->build();

        $this->userRepository
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);

        $this->userRepository
            ->expects($this->at(1))
            ->method('ofId')
            ->with($request->getFirstUser())
            ->willReturn($firstUser);

        ($this->updateUserConnectionService)($request);
    }

    public function testWhenSecondUserIsNotFoundThenThrowException()
    {
        $request = new UpdateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'approved'
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $firstUser = (new UserBuilder())
            ->withId(new Uuid($request->getFirstUser()))
            ->build();

        $this->expectException(UserNotFoundException::class);

        $this->userRepository
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        $this->userRepository
            ->expects($this->at(1))
            ->method('ofId')
            ->with($request->getFirstUser())
            ->willReturn($firstUser);
        $this->userRepository
            ->expects($this->at(2))
            ->method('ofId')
            ->with($request->getSecondUser())
            ->willReturn(null);
        ($this->updateUserConnectionService)($request);
    }

    public function testWhenUsersAreEqualThenThrowException()
    {
        $request = new UpdateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'approved'
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

        $this->userRepository
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);

        $this->userRepository
            ->method('ofId')
            ->with($request->getFirstUser())
            ->willReturn($firstUser);

        $this->userRepository
            ->method('ofId')
            ->with($request->getSecondUser())
            ->willReturn($secondUser);

        ($this->updateUserConnectionService)($request);
    }

    public function testWhenUsersRolesAreEqualThenThrowException()
    {
        $request = new UpdateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'approved'
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
        $this->userRepository
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        $this->userRepository
            ->expects($this->at(1))
            ->method('ofId')
            ->with($request->getFirstUser())
            ->willReturn($firstUser);
        $this->userRepository
            ->expects($this->at(2))
            ->method('ofId')
            ->with($request->getSecondUser())
            ->willReturn($secondUser);
        ($this->updateUserConnectionService)($request);
    }

    public function testWhenUsersRolesAreNotStudentOrTeacherThenThrowException()
    {
        $request = new UpdateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'approved'
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
        ($this->updateUserConnectionService)($request);
    }

    public function testWhenConnectionNotFoundThenThrowException()
    {
        $request = new UpdateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'approved'
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

        $this->expectException(ConnectionNotFoundException::class);

        $this->userRepository
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);

        $this->userRepository
            ->expects($this->at(1))
            ->method('ofId')
            ->with($request->getFirstUser())
            ->willReturn($firstUser);

        $this->userRepository
            ->expects($this->at(2))
            ->method('ofId')
            ->with($request->getSecondUser())
            ->willReturn($secondUser);

        $this->userConnectionRepository
            ->method('ofId')
            ->willReturn(null);

        ($this->updateUserConnectionService)($request);
    }

    public function testWhenNewStateIsInvalidThenThrowException()
    {
        $this->expectException(InvalidStateException::class);

        $request = new UpdateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'pended'
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
        $userConnection = new UserConnection(
            $firstUser->getId(),
            $secondUser->getId(),
            new Pended(),
            $firstUser->getId()
        );
        $this->userRepository
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        $this->userRepository
            ->expects($this->at(1))
            ->method('ofId')
            ->with($request->getFirstUser())
            ->willReturn($firstUser);
        $this->userRepository
            ->expects($this->at(2))
            ->method('ofId')
            ->with($request->getSecondUser())
            ->willReturn($secondUser);
        $this->stateFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn(new Pended());
        $this->userConnectionRepository
            ->expects($this->once())
            ->method('ofId')
            ->willReturn($userConnection);

        ($this->updateUserConnectionService)($request);
    }

    public function testWhenNewStateIsIncorrectThenThrowException()
    {
        $this->expectException(InvalidStateException::class);

        $request = new UpdateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'error'
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
        $userConnection = new UserConnection(
            $firstUser->getId(),
            $secondUser->getId(),
            new Pended(),
            $firstUser->getId()
        );
        $this->userRepository
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        $this->userRepository
            ->expects($this->at(1))
            ->method('ofId')
            ->with($request->getFirstUser())
            ->willReturn($firstUser);
        $this->userRepository
            ->expects($this->at(2))
            ->method('ofId')
            ->with($request->getSecondUser())
            ->willReturn($secondUser);
        $this->stateFactory
            ->method('create')
            ->willReturn(new Pended());
        $this->userConnectionRepository
            ->method('ofId')
            ->willReturn($userConnection);

        ($this->updateUserConnectionService)($request);
    }

    public function testWhenRequestIsValidThenUpdateUserConnection()
    {
        $request = new UpdateUserConnectionRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'approved'
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
        $userConnection = new UserConnection(
            $firstUser->getId(),
            $secondUser->getId(),
            new Pended(),
            $firstUser->getId()
        );
        $this->userRepository
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        $this->userRepository
            ->expects($this->at(1))
            ->method('ofId')
            ->with($request->getFirstUser())
            ->willReturn($firstUser);
        $this->userRepository
            ->expects($this->at(2))
            ->method('ofId')
            ->with($request->getSecondUser())
            ->willReturn($secondUser);
        $this->stateFactory
            ->method('create')
            ->willReturn(new Approved());
        $this->userConnectionRepository
            ->method('ofId')
            ->willReturn($userConnection);
        $this->userConnectionRepository
            ->method('save')
            ->with($userConnection);
        ($this->updateUserConnectionService)($request);
    }
}
