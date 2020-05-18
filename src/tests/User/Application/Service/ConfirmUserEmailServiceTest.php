<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\ConfirmationTokenIsExpiredException;
use LaSalle\StudentTeacher\User\Application\Exception\ConfirmationTokenNotFoundException;
use LaSalle\StudentTeacher\User\Application\Exception\IncorrectConfirmationTokenException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\ConfirmUserEmailRequest;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserPasswordRequest;
use LaSalle\StudentTeacher\User\Application\Service\ConfirmUserEmailService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ConfirmUserEmailServiceTest extends TestCase
{
    private ConfirmUserEmailService $confirmUserEmailService;
    private MockObject $repository;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->confirmUserEmailService = new ConfirmUserEmailService($this->repository);
    }

    public function testWhenUserIdIsInvalidThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        ($this->confirmUserEmailService)($this->anyConfirmUserEmailRequestWithInvalidRequestAuthorId());
    }

    public function testWhenUserIdIsNotFoundThenThrowException()
    {
        $this->expectException(UserNotFoundException::class);
        $this->repository->expects($this->once())->method('ofId')->with(
            $this->anyValidUserEmailRequest()->getUserId()
        )->willReturn(null);
        ($this->confirmUserEmailService)($this->anyValidUserEmailRequest());
    }

    public function testWhenConfirmationTokenFromUserIsNullThenThrowException()
    {
        $this->expectException(ConfirmationTokenNotFoundException::class);

        $user = $this->anyValidUser();
        $user->setConfirmationToken(null);

        $this->repository->expects($this->once())->method('ofId')->with(
            $this->anyValidUserEmailRequest()->getUserId()
        )->willReturn($user);

        ($this->confirmUserEmailService)($this->anyValidUserEmailRequest());
    }

    public function testWhenConfirmationTokenFromUserIsExpiredThenThrowException()
    {
        $this->expectException(ConfirmationTokenIsExpiredException::class);

        $user = $this->anyValidUser();
        $user->setConfirmationToken(new Token('confirmation_token'));
        $user->setExpirationDate(new \DateTimeImmutable());

        $this->repository->expects($this->once())->method('ofId')->with(
            $this->anyValidUserEmailRequest()->getUserId()
        )->willReturn($user);

        ($this->confirmUserEmailService)($this->anyValidUserEmailRequest());
    }

    public function testWhenConfirmationTokenFromUserIsNotEqualToRequestThenThrowException()
    {
        $this->expectException(IncorrectConfirmationTokenException::class);

        $user = $this->anyValidUser();
        $user->setConfirmationToken(new Token('different token'));
        $user->setExpirationDate(new \DateTimeImmutable('+ 1 day'));

        $this->repository->expects($this->once())->method('ofId')->with(
            $this->anyValidUserEmailRequest()->getUserId()
        )->willReturn($user);

        ($this->confirmUserEmailService)($this->anyValidUserEmailRequest());
    }

    public function testWhenRequestIsValidThenConfirmUserEmail()
    {
        $user = $this->anyValidUser();
        $user->setConfirmationToken(new Token('confirmation_token'));
        $user->setExpirationDate(new \DateTimeImmutable('+ 1 day'));

        $this->repository->expects($this->once())->method('ofId')->with(
            $this->anyValidUserEmailRequest()->getUserId()
        )->willReturn($user);

        $userAfterUpdate = clone $user;
        $userAfterUpdate->setConfirmationToken(null);
        $userAfterUpdate->setExpirationDate(null);
        $userAfterUpdate->setEnabled(true);

        $this->repository->expects($this->once())->method('save')->with($userAfterUpdate);

        ($this->confirmUserEmailService)($this->anyValidUserEmailRequest());
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

    private function anyConfirmUserEmailRequestWithInvalidRequestAuthorId(): ConfirmUserEmailRequest
    {
        return new ConfirmUserEmailRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753-invalid',
            'confirmation_token',
        );
    }

    private function anyValidUserEmailRequest(): ConfirmUserEmailRequest
    {
        return new ConfirmUserEmailRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'confirmation_token',
        );
    }
}