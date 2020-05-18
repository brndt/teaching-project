<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\ConfirmationTokenIsExpiredException;
use LaSalle\StudentTeacher\User\Application\Exception\ConfirmationTokenNotFoundException;
use LaSalle\StudentTeacher\User\Application\Exception\IncorrectConfirmationTokenException;
use LaSalle\StudentTeacher\User\Application\Exception\IncorrectPasswordException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\ConfirmUserPasswordResetRequest;
use LaSalle\StudentTeacher\User\Application\Service\ConfirmUserPasswordResetService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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
        $this->expectException(InvalidArgumentException::class);
        ($this->confirmUserPasswordResetService)($this->anyConfirmUserPasswordResetRequestWithInvalidRequestUserId());
    }

    public function testWhenUserIdIsNotFoundThenThrowException()
    {
        $this->expectException(UserNotFoundException::class);
        $this->repository->expects($this->once())->method('ofId')->with(
            $this->anyValidUserPasswordResetRequest()->getUserId()
        )->willReturn(null);
        ($this->confirmUserPasswordResetService)($this->anyValidUserPasswordResetRequest());
    }

    public function testWhenConfirmationTokenFromUserIsNullThenThrowException()
    {
        $this->expectException(ConfirmationTokenNotFoundException::class);

        $user = $this->anyValidUser();
        $user->setConfirmationToken(null);

        $this->repository->expects($this->once())->method('ofId')->with(
            $this->anyValidUserPasswordResetRequest()->getUserId()
        )->willReturn($user);

        ($this->confirmUserPasswordResetService)($this->anyValidUserPasswordResetRequest());
    }

    public function testWhenConfirmationTokenFromUserIsExpiredThenThrowException()
    {
        $this->expectException(ConfirmationTokenIsExpiredException::class);

        $user = $this->anyValidUser();
        $user->setConfirmationToken(new Token('confirmation_token'));
        $user->setExpirationDate(new \DateTimeImmutable());

        $this->repository->expects($this->once())->method('ofId')->with(
            $this->anyValidUserPasswordResetRequest()->getUserId()
        )->willReturn($user);

        ($this->confirmUserPasswordResetService)($this->anyValidUserPasswordResetRequest());
    }

    public function testWhenConfirmationTokenFromUserIsNotEqualToRequestThenThrowException()
    {
        $this->expectException(IncorrectConfirmationTokenException::class);

        $user = $this->anyValidUser();
        $user->setConfirmationToken(new Token('different_token'));
        $user->setExpirationDate(new \DateTimeImmutable('+ 1 day'));

        $this->repository->expects($this->once())->method('ofId')->with(
            $this->anyValidUserPasswordResetRequest()->getUserId()
        )->willReturn($user);

        ($this->confirmUserPasswordResetService)($this->anyValidUserPasswordResetRequest());
    }

    public function testWhenNewPasswordRequestIsInvalidThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);

        $user = $this->anyValidUser();
        $user->setConfirmationToken(new Token('confirmation_token'));
        $user->setExpirationDate(new \DateTimeImmutable('+ 1 day'));

        $this->repository->expects($this->once())->method('ofId')->with(
            $this->anyConfirmUserPasswordResetRequestWithInvalidNewPassword()->getUserId()
        )->willReturn($user);

        ($this->confirmUserPasswordResetService)($this->anyConfirmUserPasswordResetRequestWithInvalidNewPassword());
    }

    public function testWhenRequestIsValidThenConfirmPasswordReset()
    {
        $user = $this->anyValidUser();
        $user->setConfirmationToken(new Token('confirmation_token'));
        $user->setExpirationDate(new \DateTimeImmutable('+ 1 day'));

        $this->repository->expects($this->once())->method('ofId')->with(
            $this->anyValidUserPasswordResetRequest()->getUserId()
        )->willReturn($user);

        $userAfterUpdate = clone $user;
        $userAfterUpdate->setConfirmationToken(null);
        $userAfterUpdate->setExpirationDate(null);
        $userAfterUpdate->setPassword(Password::fromPlainPassword($this->anyValidUserPasswordResetRequest()->getNewPassword()));

        $this->repository->expects($this->once())->method('save')->with($this->callback($this->userComparator($userAfterUpdate)));

        ($this->confirmUserPasswordResetService)($this->anyValidUserPasswordResetRequest());
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
            new \DateTimeImmutable('2020-04-27'),
            false
        );
    }

    private function anyConfirmUserPasswordResetRequestWithInvalidRequestUserId(): ConfirmUserPasswordResetRequest
    {
        return new ConfirmUserPasswordResetRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753-invalid',
            'newValidPassword123',
            'confirmation_token',
        );
    }

    private function anyConfirmUserPasswordResetRequestWithInvalidNewPassword(): ConfirmUserPasswordResetRequest
    {
        return new ConfirmUserPasswordResetRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'newInvalidPassword',
            'confirmation_token',
        );
    }

    private function anyValidUserPasswordResetRequest(): ConfirmUserPasswordResetRequest
    {
        return new ConfirmUserPasswordResetRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'newValidPassword123',
            'confirmation_token',
        );
    }

    private function userComparator(User $userExpected): callable
    {
        return function (User $userActual) use ($userExpected) {
            return $userExpected->getEmail()->toString() === $userActual->getEmail()->toString()
                && $userExpected->getFirstName()->toString() === $userActual->getFirstName()->toString()
                && $userExpected->getLastName()->toString() === $userActual->getLastName()->toString()
                && $userExpected->getRoles()->getArrayOfPrimitives() === $userActual->getRoles()->getArrayOfPrimitives()
                && $userExpected->getExpirationDate() === $userActual->getExpirationDate()
                && $userExpected->getConfirmationToken() === $userActual->getConfirmationToken()
                && $userExpected->getRoles()->getArrayOfPrimitives() === $userActual->getRoles()->getArrayOfPrimitives()
                && $userExpected->getCreated() == $userActual->getCreated();
        };
    }
}