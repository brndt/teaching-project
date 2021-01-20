<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Shared\Application;

use LaSalle\StudentTeacher\User\User\Application\Exception\IncorrectPasswordException;
use LaSalle\StudentTeacher\User\Shared\Application\Exception\UserNotEnabledException;
use LaSalle\StudentTeacher\User\Shared\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Shared\Application\Request\SignInRequest;
use LaSalle\StudentTeacher\User\Shared\Application\Response\UserResponse;
use LaSalle\StudentTeacher\User\Shared\Application\Service\SignInService;
use LaSalle\StudentTeacher\User\User\Domain\Exception\InvalidEmailException;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\User\Domain\UserBuilder;

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
        $request = new SignInRequest('userexample.com', '123456aa');

        $this->expectException(InvalidEmailException::class);
        ($this->signInService)($request);
    }

    public function testWhenUserDoesntExistThenThrowException()
    {
        $request = new SignInRequest('user@xample.com', '123456aa');

        $this->repository->method('ofEmail')->willReturn(null);
        $this->expectException(UserNotFoundException::class);
        ($this->signInService)($request);
    }

    public function testWhenUserIsNotEnabledThenThrowException()
    {
        $request = new SignInRequest('user@xample.com', '123456aa');
        $user = (new UserBuilder())
            ->withEnabled(false)
            ->build();

        $this->repository->expects(self::once())->method('ofEmail')->willReturn($user);
        $this->expectException(UserNotEnabledException::class);
        ($this->signInService)($request);
    }

    public function testWhenPasswordIsIncorrectThenThrowException()
    {
        $request = new SignInRequest('user@xample.com', '123456aa1');
        $user = (new UserBuilder())->build();

        $this->repository->expects(self::once())->method('ofEmail')->willReturn($user);
        $this->expectException(IncorrectPasswordException::class);
        ($this->signInService)($request);
    }

    public function testWhenRequestIsValidThenReturnUser()
    {
        $request = new SignInRequest('user@xample.com', '123456aa');
        $user = (new UserBuilder())->build();
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

        $this->repository->expects(self::once())->method('ofEmail')->willReturn($user);
        $userResponse = ($this->signInService)($request);
        $this->assertEquals($expectedUserResponse, $userResponse);
    }
}
