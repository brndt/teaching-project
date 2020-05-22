<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\SearchUserCredentialsByIdRequest;
use LaSalle\StudentTeacher\User\Application\Response\UserCredentialsResponse;
use LaSalle\StudentTeacher\User\Application\Service\SearchUserCredentialsByIdService;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class SearchUserCredentialsByIdServiceTest extends TestCase
{
    private SearchUserCredentialsByIdService $searchUserCredentialsByIdService;
    private MockObject $repository;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->searchUserCredentialsByIdService = new SearchUserCredentialsByIdService($this->repository);
    }

    public function testWhenUserIdIsInvalidThenThrowException()
    {
        $request = new SearchUserCredentialsByIdRequest(Uuid::generate()->toString() . 'invalidCharacters');

        $this->expectException(InvalidArgumentException::class);
        ($this->searchUserCredentialsByIdService)($request);
    }

    public function testWhenUserIdIsNotFoundThenThrowException()
    {
        $request = new SearchUserCredentialsByIdRequest(Uuid::generate()->toString());

        $this->expectException(UserNotFoundException::class);
        $this->repository->expects($this->once())->method('ofId')->with($request->getUserId())->willReturn(null);
        ($this->searchUserCredentialsByIdService)($request);
    }

    public function testWhenRequestIsValidThenReturnUserCreadentials()
    {
        $request = new SearchUserCredentialsByIdRequest(Uuid::generate()->toString());
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->build();
        $expectedUserCredentialsResponse = new UserCredentialsResponse(
            $user->getId()->toString(),
            $user->getEmail()->toString(),
            $user->getPassword()->toString(),
            $user->getRoles()->getArrayOfPrimitives(),
            $user->getEnabled(),
        );

        $this->repository->expects($this->once())->method('ofId')->with($user->getId())->willReturn($user);
        $userCredentialsResponse = ($this->searchUserCredentialsByIdService)($request);

        $this->assertEquals($expectedUserCredentialsResponse, $userCredentialsResponse);
    }
}