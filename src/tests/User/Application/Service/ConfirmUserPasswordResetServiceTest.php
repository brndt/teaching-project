<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use DateTimeImmutable;
use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\ConfirmationTokenIsExpiredException;
use LaSalle\StudentTeacher\User\Application\Exception\ConfirmationTokenNotFoundException;
use LaSalle\StudentTeacher\User\Application\Exception\IncorrectConfirmationTokenException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\ConfirmUserPasswordResetRequest;
use LaSalle\StudentTeacher\User\Application\Service\ConfirmUserPasswordResetService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidNumberContainingException;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class ConfirmUserPasswordResetServiceTest extends TestCase
{
    private ConfirmUserPasswordResetService $confirmUserPasswordResetService;
    private MockObject $repository;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->confirmUserPasswordResetService = new ConfirmUserPasswordResetService($this->repository);
    }

    public function testWhenUserIdIsInvalidThenThrowException()
    {
        $this->expectException(InvalidUuidException::class);

        $request = new ConfirmUserPasswordResetRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753-invalid',
            'newValidPassword123',
            'confirmation_token',
        );

        ($this->confirmUserPasswordResetService)($request);
    }

    public function testWhenUserIdIsNotFoundThenThrowException()
    {
        $request = new ConfirmUserPasswordResetRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'newValidPassword123',
            'confirmation_token',
        );

        $this->expectException(UserNotFoundException::class);
        $this->repository->expects($this->once())->method('ofId')->with($request->getUserId())->willReturn(null);
        ($this->confirmUserPasswordResetService)($request);
    }

    public function testWhenConfirmationTokenFromUserIsNullThenThrowException()
    {
        $request = new ConfirmUserPasswordResetRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'newValidPassword123',
            'confirmation_token',
        );
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->withConfirmationToken(null)
            ->build();

        $this->expectException(ConfirmationTokenNotFoundException::class);
        $this->repository->expects($this->once())->method('ofId')->with($request->getUserId())->willReturn($user);
        ($this->confirmUserPasswordResetService)($request);
    }

    public function testWhenConfirmationTokenFromUserIsExpiredThenThrowException()
    {
        $request = new ConfirmUserPasswordResetRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'newValidPassword123',
            'confirmation_token',
        );
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->withConfirmationToken(new Token('confirmation_token'))
            ->withExpirationDate(new DateTimeImmutable())
            ->build();

        $this->expectException(ConfirmationTokenIsExpiredException::class);
        $this->repository->expects($this->once())->method('ofId')->with($request->getUserId())->willReturn($user);
        ($this->confirmUserPasswordResetService)($request);
    }

    public function testWhenConfirmationTokenFromUserIsNotEqualToRequestThenThrowException()
    {
        $request = new ConfirmUserPasswordResetRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'newValidPassword123',
            'confirmation_token',
        );
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->withConfirmationToken(new Token('different_token'))
            ->withExpirationDate(new DateTimeImmutable('+ 1 day'))
            ->build();

        $this->expectException(IncorrectConfirmationTokenException::class);

        $this->repository->expects($this->once())->method('ofId')->with($request->getUserId())->willReturn($user);
        ($this->confirmUserPasswordResetService)($request);
    }

    public function testWhenNewPasswordRequestIsInvalidThenThrowException()
    {
        $request = new ConfirmUserPasswordResetRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'invalidPassword',
            'confirmation_token',
        );
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->withConfirmationToken(new Token('confirmation_token'))
            ->withExpirationDate(new DateTimeImmutable('+ 1 day'))
            ->build();

        $this->expectException(InvalidNumberContainingException::class);
        $this->repository->expects($this->once())->method('ofId')->with($request->getUserId())->willReturn($user);
        ($this->confirmUserPasswordResetService)($request);
    }

    public function testWhenRequestIsValidThenConfirmPasswordReset()
    {
        $request = new ConfirmUserPasswordResetRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'newValidPassword123',
            'confirmation_token',
        );
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->withConfirmationToken(new Token('confirmation_token'))
            ->withExpirationDate(new DateTimeImmutable('+ 1 day'))
            ->build();
        $userAfterUpdate = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->withPassword(Password::fromPlainPassword($request->getNewPassword()))
            ->withConfirmationToken(null)
            ->withExpirationDate(null)
            ->build();

        $this->repository->expects($this->once())->method('ofId')->with($request->getUserId())->willReturn($user);
        $this->repository->expects($this->once())->method('save')->with(
            $this->callback($this->userComparator($userAfterUpdate, $request->getNewPassword()))
        );
        ($this->confirmUserPasswordResetService)($request);
    }

    public function userComparator(User $userExpected, string $plainPassword): callable
    {
        return function (User $userActual) use ($userExpected, $plainPassword) {
            return
                $userExpected->getId()->toString() === $userActual->getId()->toString()
                && $userExpected->getEmail()->toString() === $userActual->getEmail()->toString()
                && password_verify($plainPassword, $userActual->getPassword()->toString())
                && $userExpected->getFirstName()->toString() === $userActual->getFirstName()->toString()
                && $userExpected->getLastName()->toString() === $userActual->getLastName()->toString()
                && $userExpected->getRoles()->getArrayOfPrimitives() === $userActual->getRoles()->getArrayOfPrimitives()
                && $userExpected->getExpirationDate() === $userActual->getExpirationDate()
                && $userExpected->getConfirmationToken() === $userActual->getConfirmationToken()
                && $userExpected->getCreated()->diff($userActual->getCreated())->s < 10;
        };
    }
}
