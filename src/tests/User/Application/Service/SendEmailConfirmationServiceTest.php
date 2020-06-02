<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use DateTimeImmutable;
use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Domain\RandomStringGenerator;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\SendEmailConfirmationRequest;
use LaSalle\StudentTeacher\User\Application\Service\SendEmailConfirmationService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\EmailSender;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class SendEmailConfirmationServiceTest extends TestCase
{
    private SendEmailConfirmationService $sendEmailConfirmationService;
    private MockObject $repository;
    private MockObject $emailSender;
    private MockObject $randomStringGenerator;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->emailSender = $this->createMock(EmailSender::class);
        $this->randomStringGenerator = $this->createMock(RandomStringGenerator::class);
        $this->sendEmailConfirmationService = new SendEmailConfirmationService(
            $this->emailSender,
            $this->randomStringGenerator,
            $this->repository
        );
    }

    public function testWhenUserEmailIsInvalidThenThrowException()
    {
        $request = new SendEmailConfirmationRequest('userexample.com');
        $this->expectException(InvalidArgumentException::class);
        ($this->sendEmailConfirmationService)($request);
    }

    public function testWhenUserNotFoundThenThrowException()
    {
        $request = new SendEmailConfirmationRequest('user@example.com');
        $this->repository->method('ofEmail')->willReturn(null);
        $this->expectException(UserNotFoundException::class);
        ($this->sendEmailConfirmationService)($request);
    }

    public function testWhenValidRequestThenSendEmailConfirmation()
    {
        $request = new SendEmailConfirmationRequest('user@example.com');
        $userToSendEmail = (new UserBuilder())
            ->withConfirmationToken(new Token('random_token'))
            ->withExpirationDate(new DateTimeImmutable('+ 1 day'))
            ->build();
        $this->repository->method('ofEmail')->willReturn($userToSendEmail);
        $this->randomStringGenerator->method('generate')->willReturn('random_token');
        $this->repository->expects($this->once())->method('save')->with(
            $this->callback($this->userComparator($userToSendEmail))
        );
        $this->emailSender->expects($this->once())->method('sendEmailConfirmation')->with(
            $userToSendEmail->getEmail(),
            $userToSendEmail->getId(),
            $userToSendEmail->getFirstName(),
            $userToSendEmail->getLastName(),
            $userToSendEmail->getConfirmationToken()
        );

        ($this->sendEmailConfirmationService)($request);
    }

    private function userComparator(User $userExpected): callable
    {
        return function (User $userActual) use ($userExpected) {
            return $userExpected->getExpirationDate() === $userActual->getExpirationDate()
                && $userExpected->getConfirmationToken() === $userActual->getConfirmationToken();
        };
    }
}