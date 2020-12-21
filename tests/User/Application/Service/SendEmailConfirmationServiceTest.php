<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use DateTimeImmutable;
use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEventBus;
use LaSalle\StudentTeacher\Shared\Domain\RandomStringGenerator;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\SendEmailConfirmationRequest;
use LaSalle\StudentTeacher\User\Application\Service\SendEmailConfirmationService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Event\EmailConfirmationRequestReceivedDomainEvent;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidEmailException;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class SendEmailConfirmationServiceTest extends TestCase
{
    private SendEmailConfirmationService $sendEmailConfirmationService;
    private MockObject $repository;
    private MockObject $eventBus;
    private MockObject $randomStringGenerator;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->eventBus = $this->createMock(DomainEventBus::class);
        $this->randomStringGenerator = $this->createMock(RandomStringGenerator::class);
        $this->sendEmailConfirmationService = new SendEmailConfirmationService(
            $this->eventBus,
            $this->randomStringGenerator,
            $this->repository
        );
    }

    public function testWhenUserEmailIsInvalidThenThrowException()
    {
        $this->expectException(InvalidEmailException::class);

        $request = new SendEmailConfirmationRequest('userexample.com');
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
            ->withId(Uuid::generate())
            ->withConfirmationToken(new Token('random_token'))
            ->withExpirationDate(new DateTimeImmutable('+ 1 day'))
            ->build();
        $this->repository->method('ofEmail')->willReturn($userToSendEmail);
        $this->randomStringGenerator->method('generate')->willReturn('random_token');
        $this->repository->expects($this->once())->method('save')->with(
            $this->callback($this->userComparator($userToSendEmail))
        );
        $event = new EmailConfirmationRequestReceivedDomainEvent(
            $userToSendEmail->getId()->toString(),
            $userToSendEmail->getEmail()->toString(),
            $userToSendEmail->getFirstName()->toString(),
            $userToSendEmail->getLastName()->toString(),
            $userToSendEmail->getConfirmationToken()->toString()
        );
        $this->eventBus->expects($this->once())->method('dispatch')->with($this->callback($this->domainEventComparator($event)));

        ($this->sendEmailConfirmationService)($request);
    }

    private function userComparator(User $userExpected): callable
    {
        return function (User $userActual) use ($userExpected) {
            return $userExpected->getExpirationDate() === $userActual->getExpirationDate()
                && $userExpected->getConfirmationToken() === $userActual->getConfirmationToken();
        };
    }

    private function domainEventComparator(EmailConfirmationRequestReceivedDomainEvent $eventExpected): callable
    {
        return function (EmailConfirmationRequestReceivedDomainEvent $eventActual) use ($eventExpected) {
            return $eventExpected->getAggregateId() === $eventActual->getAggregateId()
                && $eventExpected->getEmail() === $eventActual->getEmail()
                && $eventExpected->getFirstName() === $eventActual->getFirstName()
                && $eventExpected->getLastName() === $eventActual->getLastName()
                && $eventExpected->getConfirmationToken() === $eventActual->getConfirmationToken();
        };
    }
}
