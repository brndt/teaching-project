<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\IncorrectPasswordException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotEnabledException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\CreateUserRequest;
use LaSalle\StudentTeacher\User\Application\Request\SignInRequest;
use LaSalle\StudentTeacher\User\Application\Response\UserResponse;
use LaSalle\StudentTeacher\User\Application\Service\SignInService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SignInServiceTest extends TestCase
{
    private SignInService $signInService;
    private MockObject $repository;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->signInService = new SignInService($this->repository);
    }

    public function testWhenUserEmailIsInvalidThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        ($this->signInService)($this->anyUserRequestWithInvalidEmail());
    }

    public function testWhenUserDoesntExistThenThrowException()
    {
        $this->repository->method('ofEmail')->willReturn(null);
        $this->expectException(UserNotFoundException::class);
        ($this->signInService)($this->anyValidUserRequest());
    }

    public function testWhenUserIsNotEnabledThenThrowException()
    {
        $this->repository->expects($this->once())->method('ofEmail')->willReturn($this->anyNotEnabledUser());
        $this->expectException(UserNotEnabledException::class);
        ($this->signInService)($this->anyValidUserRequest());
    }

    public function testWhenPasswordIsIncorrectThenThrowException()
    {
        $this->repository->expects($this->once())->method('ofEmail')->willReturn($this->anyValidUser());
        $this->expectException(IncorrectPasswordException::class);
        ($this->signInService)($this->anyUserWithIncorrectPasswordRequest());
    }

    public function testWhenRequestIsValidThenReturnUser()
    {
        $this->repository->expects($this->once())->method('ofEmail')->willReturn($this->anyValidUser());
        $userResponse = ($this->signInService)($this->anyValidUserRequest());
        $this->assertEquals($this->anyValidUserResponse(), $userResponse);
    }

    private function anyUserRequestWithInvalidEmail(): SignInRequest
    {
        return new SignInRequest(
            'userexample.com',
            '123456aa'
        );
    }

    private function anyValidUserRequest(): SignInRequest
    {
        return new SignInRequest(
            'user@xample.com',
            '123456aa'
        );
    }

    private function anyUserWithIncorrectPasswordRequest(): SignInRequest
    {
        return new SignInRequest(
            'user@xample.com',
            '123456aa1'
        );
    }

    private function anyValidUser(): User
    {
        return new User(
            new Uuid('16bf6c6a-c855-4a36-a3dd-5b9f6d92c753'),
            new Email('user@example.com'),
            Password::fromPlainPassword('123456aa'),
            new Name('Alex'),
            new Name('Johnson'),
            Roles::fromArrayOfPrimitives(['teacher']),
            new \DateTimeImmutable('2020-04-28 15:00:00'),
            true
        );
    }

    private function anyNotEnabledUser(): User
    {
        return new User(
            new Uuid('16bf6c6a-c855-4a36-a3dd-5b9f6d92c753'),
            new Email('user@example.com'),
            Password::fromPlainPassword('123456aa'),
            new Name('Alex'),
            new Name('Johnson'),
            Roles::fromArrayOfPrimitives(['teacher']),
            new \DateTimeImmutable('2020-04-28 15:00:00'),
            false
        );
    }

    private function anyValidUserResponse(): UserResponse
    {
        return new UserResponse(
            $this->anyValidUser()->getId()->toString(),
            $this->anyValidUser()->getFirstName()->toString(),
            $this->anyValidUser()->getLastName()->toString(),
            $this->anyValidUser()->getRoles()->getArrayOfPrimitives(),
            $this->anyValidUser()->getCreated(),
            $this->anyValidUser()->getImage(),
            $this->anyValidUser()->getEducation(),
            $this->anyValidUser()->getExperience(),
        );
    }
}