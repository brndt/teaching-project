<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Domain\RandomStringGenerator;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotEnabledException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\SendPasswordResetRequest;
use LaSalle\StudentTeacher\User\Application\Service\SendPasswordResetService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\EmailSender;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SendPasswordResetServiceTest extends TestCase
{
    private SendPasswordResetService $sendPasswordResetService;
    private MockObject $repository;
    private MockObject $emailSender;
    private MockObject $randomStringGenerator;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->emailSender = $this->createMock(EmailSender::class);
        $this->randomStringGenerator = $this->createMock(RandomStringGenerator::class);
        $this->sendPasswordResetService = new SendPasswordResetService(
            $this->emailSender,
            $this->randomStringGenerator,
            $this->repository
        );
    }

    public function testWhenUserEmailIsInvalidThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        ($this->sendPasswordResetService)($this->anySendPasswordResetRequestWithInvalidEmail());
    }

    public function testWhenUserNotFoundThenThrowException()
    {
        $this->repository->method('ofEmail')->willReturn(null);
        $this->expectException(UserNotFoundException::class);
        ($this->sendPasswordResetService)($this->anyValidSendPasswordResetRequest());
    }

    public function testWhenUserIsNotEnabledThenThrowException()
    {
        $this->repository->method('ofEmail')->willReturn($this->anyValidUser());
        $this->expectException(UserNotEnabledException::class);
        ($this->sendPasswordResetService)($this->anyValidSendPasswordResetRequest());
    }

    public function testWhenValidRequestThenSendPasswordReset()
    {
        $userToSendEmail = $this->anyValidUser();
        $userToSendEmail->setEnabled(true);
        $this->repository->method('ofEmail')->willReturn($userToSendEmail);

        $this->randomStringGenerator->method('generate')->willReturn('random_token');

        $userToSendEmail->setConfirmationToken(new Token('random_token'));
        $userToSendEmail->setExpirationDate(new \DateTimeImmutable('+1 day'));

        $this->repository->expects($this->once())->method('save')->with(
            $this->callback($this->userComparator($userToSendEmail))
        );

        $this->emailSender->expects($this->once())->method('sendPasswordReset')->with(
            $userToSendEmail->getEmail(),
            $userToSendEmail->getId(),
            $userToSendEmail->getFirstName(),
            $userToSendEmail->getLastName(),
            $userToSendEmail->getConfirmationToken()
        );

        ($this->sendPasswordResetService)($this->anyValidSendPasswordResetRequest());
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

    private function anySendPasswordResetRequestWithInvalidEmail()
    {
        return new SendPasswordResetRequest(
            'userexample.com'
        );
    }

    private function anyValidSendPasswordResetRequest(): SendPasswordResetRequest
    {
        return new SendPasswordResetRequest(
            'user@example.com'
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