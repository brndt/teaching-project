<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\SearchUserCredentialsByIdRequest;
use LaSalle\StudentTeacher\User\Application\Response\UserCredentialsResponse;
use LaSalle\StudentTeacher\User\Application\Service\SearchUserCredentialsByIdService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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
        $this->expectException(InvalidArgumentException::class);
        ($this->searchUserCredentialsByIdService)($this->anyRequestWithInvalidUserId());
    }

    public function testWhenUserIdIsNotFoundThenThrowException()
    {
        $this->expectException(UserNotFoundException::class);
        $this->repository->expects($this->once())->method('ofId')->with(
            $this->anyValidRequest()->getUserId()
        )->willReturn(null);
        ($this->searchUserCredentialsByIdService)($this->anyValidRequest());
    }

    public function testWhenRequestIsValidThenReturnUserCreadentials()
    {
        $user = $this->anyValidUser();

        $this->repository->expects($this->once())->method('ofId')->with(
            $this->anyValidUser()->getId()
        )->willReturn($user);

        $userCredentialsResponse = ($this->searchUserCredentialsByIdService)($this->anyValidRequest());
        $this->assertEquals($this->anyValidUserCredentialsResponse(), $userCredentialsResponse);
    }

    private function anyValidUser(): User
    {
        return new User(
            new Uuid('16bf6c6a-c855-4a36-a3dd-5b9f6d92c753'),
            new Email('user@example.com'),
            Password::fromHashedPassword('$2y$10$p7s2XiFvYtXIJIfkZxyyMuMUn7/7TDnDBmCXRXOWienN45/oph1we'),
            new Name('Alex'),
            new Name('Johnson'),
            Roles::fromArrayOfPrimitives(['teacher']),
            new \DateTimeImmutable('2020-04-27'),
            false
        );
    }

    private function anyValidUserCredentialsResponse(): UserCredentialsResponse
    {
        return new UserCredentialsResponse(
            $this->anyValidUser()->getId()->toString(),
            $this->anyValidUser()->getEmail()->toString(),
            $this->anyValidUser()->getPassword()->toString(),
            $this->anyValidUser()->getRoles()->getArrayOfPrimitives(),
            $this->anyValidUser()->getEnabled(),
        );
    }

    private function anyRequestWithInvalidUserId(): SearchUserCredentialsByIdRequest
    {
        return new SearchUserCredentialsByIdRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753-invalid',
        );
    }

    private function anyValidRequest(): SearchUserCredentialsByIdRequest
    {
        return new SearchUserCredentialsByIdRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
        );
    }
}