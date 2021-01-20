<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\User\Application\Service;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEventBus;
use LaSalle\StudentTeacher\Shared\Domain\RandomStringGenerator;
use LaSalle\StudentTeacher\User\Shared\Application\Exception\UserNotEnabledException;
use LaSalle\StudentTeacher\User\Shared\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\User\Application\Request\SendPasswordResetRequest;
use LaSalle\StudentTeacher\User\User\Application\Service\SendPasswordResetService;
use LaSalle\StudentTeacher\User\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\User\Domain\Event\PasswordResetRequestReceivedDomainEvent;
use LaSalle\StudentTeacher\User\User\Domain\Exception\InvalidEmailException;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\User\Domain\UserBuilder;

final class SendPasswordResetServiceTest extends TestCase
{
    private SendPasswordResetService $sendPasswordResetService;
    private MockObject $repository;
    private MockObject $eventBus;
    private MockObject $randomStringGenerator;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->eventBus = $this->createMock(DomainEventBus::class);
        $this->randomStringGenerator = $this->createMock(RandomStringGenerator::class);
        $this->sendPasswordResetService = new SendPasswordResetService(
            $this->randomStringGenerator,
            $this->eventBus,
            $this->repository
        );
    }

    public function testWhenUserEmailIsInvalidThenThrowException()
    {
        $this->expectException(InvalidEmailException::class);

        $request = new SendPasswordResetRequest('userexample.com');
        ($this->sendPasswordResetService)($request);
    }

    public function testWhenUserNotFoundThenThrowException()
    {
        $request = new SendPasswordResetRequest('user@example.com');
        $this->repository->method('ofEmail')->willReturn(null);
        $this->expectException(UserNotFoundException::class);
        ($this->sendPasswordResetService)($request);
    }

    public function testWhenUserIsNotEnabledThenThrowException()
    {
        $request = new SendPasswordResetRequest('user@example.com');
        $userToSendEmail = (new UserBuilder())
            ->withEnabled(false)
            ->build();

        $this->expectException(UserNotEnabledException::class);
        $this->repository->method('ofEmail')->willReturn($userToSendEmail);
        ($this->sendPasswordResetService)($request);
    }

    public function testWhenValidRequestThenSendPasswordReset()
    {
        $request = new SendPasswordResetRequest('user@example.com');
        $userToSendEmail = (new UserBuilder())
            ->withConfirmationToken(new Token('random_token'))
            ->withExpirationDate(new DateTimeImmutable('+ 1 day'))
            ->withEnabled(true)
            ->build();

        $this->repository->method('ofEmail')->willReturn($userToSendEmail);
        $this->randomStringGenerator->method('generate')->willReturn('random_token');
        $this->repository->expects(self::once())->method('save')->with(
            $this->callback($this->userComparator($userToSendEmail))
        );
        $event = new PasswordResetRequestReceivedDomainEvent(
            $userToSendEmail->getId()->toString(),
            $userToSendEmail->getEmail()->toString(),
            $userToSendEmail->getFirstName()->toString(),
            $userToSendEmail->getLastName()->toString(),
            $userToSendEmail->getConfirmationToken()->toString()
        );
        $this->eventBus->expects(self::once())->method('dispatch')->with(
            $this->callback($this->domainEventComparator($event))
        );

        ($this->sendPasswordResetService)($request);
    }

    private function userComparator(User $userExpected): callable
    {
        return function (User $userActual) use ($userExpected) {
            return $userExpected->getEnabled() === $userActual->getEnabled()
                && $userExpected->getExpirationDate() === $userActual->getExpirationDate()
                && $userExpected->getConfirmationToken() === $userActual->getConfirmationToken();
        };
    }

    private function domainEventComparator(PasswordResetRequestReceivedDomainEvent $eventExpected): callable
    {
        return function (PasswordResetRequestReceivedDomainEvent $eventActual) use ($eventExpected) {
            return $eventExpected->getAggregateId() === $eventActual->getAggregateId()
                && $eventExpected->getEmail() === $eventActual->getEmail()
                && $eventExpected->getFirstName() === $eventActual->getFirstName()
                && $eventExpected->getLastName() === $eventActual->getLastName()
                && $eventExpected->getConfirmationToken() === $eventActual->getConfirmationToken();
        };
    }
}
