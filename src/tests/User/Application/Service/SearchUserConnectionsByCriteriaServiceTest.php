<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\ConnectionNotFoundException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\SearchUserConnectionsByCriteriaRequest;
use LaSalle\StudentTeacher\User\Application\Response\UserConnectionCollectionResponse;
use LaSalle\StudentTeacher\User\Application\Response\UserConnectionResponse;
use LaSalle\StudentTeacher\User\Application\Service\SearchUserConnectionsByCriteriaService;
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

final class SearchUserConnectionsByCriteriaServiceTest extends TestCase
{
    private SearchUserConnectionsByCriteriaService $searchUserConnectionService;
    protected MockObject $userConnectionRepository;
    protected MockObject $userRepository;
    protected MockObject $stateFactory;

    public function setUp(): void
    {
        $this->userConnectionRepository = $this->createMock(UserConnectionRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->stateFactory = $this->createMock(StateFactory::class);
        $this->searchUserConnectionService = new SearchUserConnectionsByCriteriaService(
            $this->userConnectionRepository,
            $this->userRepository,
            $this->stateFactory
        );
    }

    public function testWhenRequestAuthorIsInvalidThenThrowException()
    {
        $request = new SearchUserConnectionsByCriteriaRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138-invalid',
            'cfe849f3-7832-435a-b484-83fabf530794',
            null,
            null,
            null,
            null,
            null
        );

        $this->expectException(InvalidArgumentException::class);
        ($this->searchUserConnectionService)($request);
    }

    public function testWhenRequestAuthorIsNotFoundThenThrowException()
    {
        $request = new SearchUserConnectionsByCriteriaRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            null,
            null,
            null,
            null,
            null
        );

        $this->expectException(UserNotFoundException::class);
        $this->userRepository
            ->expects($this->once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn(null);
        ($this->searchUserConnectionService)($request);
    }

    public function testWhenFirstUserIdIsInvalidThenThrowException()
    {
        $request = new SearchUserConnectionsByCriteriaRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794-invalid',
            null,
            null,
            null,
            null,
            null
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $this->expectException(InvalidArgumentException::class);
        $this->userRepository->expects($this->at(0))->method('ofId')->with(
            $request->getRequestAuthorId()
        )->willReturn($author);
        ($this->searchUserConnectionService)($request);
    }

    public function testWhenFirstUserIsNotFoundThenThrowException()
    {
        $request = new SearchUserConnectionsByCriteriaRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            null,
            null,
            null,
            null,
            null
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
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
            ->with($request->getUserId())
            ->willReturn(null);
        ($this->searchUserConnectionService)($request);
    }

    public function testWhenRequestAuthorHasntPermissionsThenThrowException()
    {
        $request = new SearchUserConnectionsByCriteriaRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            'cfe849f3-7832-435a-b484-83fabf530794',
            null,
            null,
            null,
            null,
            null
        );

        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::TEACHER]))
            ->build();
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->build();


        $this->expectException(PermissionDeniedException::class);
        $this->userRepository->expects($this->at(0))->method('ofId')->with(
            $request->getRequestAuthorId()
        )->willReturn($author);
        $this->userRepository->expects($this->at(1))->method('ofId')->with(
            $request->getUserId()
        )->willReturn($user);
        ($this->searchUserConnectionService)($request);
    }

    public function testWhenConnectionIsNotFoundThenReturnEmptyResult()
    {
        $request = new SearchUserConnectionsByCriteriaRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            null,
            null,
            null,
            null,
            null
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $firstUser = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->build();
        $expectedUserConnectionCollectionResponse = new UserConnectionCollectionResponse(
            ...
            $this->buildStudentResponse(...[])
        );

        $this->userRepository->expects($this->at(0))->method('ofId')->with(
            $request->getRequestAuthorId()
        )->willReturn($author);
        $this->userRepository->expects($this->at(1))->method('ofId')->with(
            $request->getUserId()
        )->willReturn($firstUser);
        $this->userConnectionRepository->expects($this->once())->method('matching')->willReturn([]);

        $userConnectionCollectionResponse = ($this->searchUserConnectionService)($request);
        $this->assertEquals($expectedUserConnectionCollectionResponse, $userConnectionCollectionResponse);
    }

    public function testWhenRequestIsValidAndAuthorIsStudentThenReturnUserConnection()
    {
        $request = new SearchUserConnectionsByCriteriaRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            null,
            null,
            null,
            null,
            null
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::STUDENT]))
            ->build();
        $otherUser = (new UserBuilder())->build();
        $userConnection = new UserConnection($user->getId(), $otherUser->getId(), new Pended(), $user->getId());
        $expectedUserConnectionCollectionResponse = new UserConnectionCollectionResponse(
            ...
            $this->buildStudentResponse($userConnection)
        );

        $this->userRepository->expects($this->at(0))->method('ofId')->with(
            $request->getRequestAuthorId()
        )->willReturn($author);
        $this->userRepository->expects($this->at(1))->method('ofId')->with(
            $user->getId()
        )->willReturn($user);
        $this->userConnectionRepository->expects($this->once())->method('matching')->willReturn([$userConnection]);

        $userConnectionCollectionResponse = ($this->searchUserConnectionService)($request);
        $this->assertEquals($expectedUserConnectionCollectionResponse, $userConnectionCollectionResponse);
    }

    private function buildStudentResponse(UserConnection ...$connections): array
    {
        return array_map(
            static function (UserConnection $connection) {
                return new UserConnectionResponse(
                    $connection->getStudentId()->toString(),
                    $connection->getTeacherId()->toString(),
                    (string)$connection->getState(),
                    $connection->getSpecifierId()->toString()
                );
            },
            $connections
        );
    }

    public function testWhenRequestIsValidAndAuthorIsTeacherThenSearchUserConnection()
    {
        $request = new SearchUserConnectionsByCriteriaRequest(
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            '48d34c63-6bba-4c72-a461-8aac1fd7d138',
            null,
            null,
            null,
            null,
            null
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->withRoles(Roles::fromArrayOfPrimitives([Role::TEACHER]))
            ->build();
        $otherUser = (new UserBuilder())->build();
        $userConnection = new UserConnection($user->getId(), $otherUser->getId(), new Pended(), $user->getId());
        $expectedUserConnectionCollectionResponse = new UserConnectionCollectionResponse(
            ...
            $this->buildTeacherResponse($userConnection)
        );

        $this->userRepository->expects($this->at(0))->method('ofId')->with(
            $request->getRequestAuthorId()
        )->willReturn($author);
        $this->userRepository->expects($this->at(1))->method('ofId')->with(
            $user->getId()
        )->willReturn($user);
        $this->userConnectionRepository->expects($this->once())->method('matching')->willReturn([$userConnection]);

        $userConnectionCollectionResponse = ($this->searchUserConnectionService)($request);
        $this->assertEquals($expectedUserConnectionCollectionResponse, $userConnectionCollectionResponse);
    }

    private function buildTeacherResponse(UserConnection ...$connections): array
    {
        return array_map(
            static function (UserConnection $connection) {
                return new UserConnectionResponse(
                    $connection->getTeacherId()->toString(),
                    $connection->getStudentId()->toString(),
                    (string)$connection->getState(),
                    $connection->getSpecifierId()->toString()
                );
            },
            $connections
        );
    }

}
