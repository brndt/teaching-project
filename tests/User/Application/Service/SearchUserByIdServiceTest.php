<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\SearchUserByIdRequest;
use LaSalle\StudentTeacher\User\Application\Response\UserResponse;
use LaSalle\StudentTeacher\User\Application\Service\SearchUserByIdService;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class SearchUserByIdServiceTest extends TestCase
{
    private SearchUserByIdService $searchUserByIdService;
    private MockObject $repository;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->searchUserByIdService = new SearchUserByIdService($this->repository);
    }

    public function testWhenUserIdIsInvalidThenThrowException()
    {
        $this->expectException(InvalidUuidException::class);

        $request = new SearchUserByIdRequest(Uuid::generate()->toString() . 'invalidCharacters');

        ($this->searchUserByIdService)($request);
    }

    public function testWhenUserIdIsNotFoundThenThrowException()
    {
        $request = new SearchUserByIdRequest(Uuid::generate()->toString());

        $this->expectException(UserNotFoundException::class);
        $this->repository->expects(self::once())->method('ofId')->with($request->getUserId())->willReturn(null);
        ($this->searchUserByIdService)($request);
    }

    public function testWhenRequestIsValidThenReturnUserResponse()
    {
        $request = new SearchUserByIdRequest(Uuid::generate()->toString());
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->build();
        $expectedUserResponse = new UserResponse(
            $user->getId()->toString(),
            $user->getFirstName()->toString(),
            $user->getLastName()->toString(),
            $user->getRoles()->getArrayOfPrimitives(),
            $user->getCreated(),
            $user->getImage(),
            $user->getExperience(),
            $user->getEducation(),
        );

        $this->repository->expects(self::once())->method('ofId')->with($user->getId())->willReturn($user);
        $userResponse = ($this->searchUserByIdService)($request);

        $this->assertEquals($expectedUserResponse, $userResponse);
    }
}
