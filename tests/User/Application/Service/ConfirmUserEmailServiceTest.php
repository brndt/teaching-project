<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\ConfirmationTokenIsExpiredException;
use LaSalle\StudentTeacher\User\Application\Exception\ConfirmationTokenNotFoundException;
use LaSalle\StudentTeacher\User\Application\Exception\IncorrectConfirmationTokenException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\ConfirmUserEmailRequest;
use LaSalle\StudentTeacher\User\Application\Service\ConfirmUserEmailService;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

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
        $this->expectException(InvalidUuidException::class);

        $request = new ConfirmUserEmailRequest('16bf6c6a-c855-4a36-a3dd-5b9f6d92c753-invalid', 'confirmation_token');

        ($this->confirmUserEmailService)($request);
    }

    public function testWhenUserIdIsNotFoundThenThrowException()
    {
        $request = new ConfirmUserEmailRequest('16bf6c6a-c855-4a36-a3dd-5b9f6d92c753', 'confirmation_token');

        $this->expectException(UserNotFoundException::class);
        $this->repository->expects(self::once())->method('ofId')->with(new Uuid($request->getUserId()))->willReturn(
            null
        );
        ($this->confirmUserEmailService)($request);
    }

    public function testWhenConfirmationTokenFromUserIsNullThenThrowException()
    {
        $request = new ConfirmUserEmailRequest('16bf6c6a-c855-4a36-a3dd-5b9f6d92c753', 'confirmation_token');
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->withConfirmationToken(null)
            ->build();

        $this->expectException(ConfirmationTokenNotFoundException::class);
        $this->repository->expects(self::once())->method('ofId')->with($user->getId())->willReturn($user);
        ($this->confirmUserEmailService)($request);
    }

    public function testWhenConfirmationTokenFromUserIsExpiredThenThrowException()
    {
        $request = new ConfirmUserEmailRequest('16bf6c6a-c855-4a36-a3dd-5b9f6d92c753', 'confirmation_token');
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->withConfirmationToken(new Token($request->getConfirmationToken()))
            ->withExpirationDate(new DateTimeImmutable())
            ->build();

        $this->expectException(ConfirmationTokenIsExpiredException::class);
        $this->repository->expects(self::once())->method('ofId')->with($user->getId())->willReturn($user);
        ($this->confirmUserEmailService)($request);
    }

    public function testWhenConfirmationTokenFromUserIsNotEqualToRequestThenThrowException()
    {
        $request = new ConfirmUserEmailRequest('16bf6c6a-c855-4a36-a3dd-5b9f6d92c753', 'different_token');
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->withConfirmationToken(new Token('confirmation_token'))
            ->withExpirationDate(new DateTimeImmutable('+1 day'))
            ->build();

        $this->expectException(IncorrectConfirmationTokenException::class);
        $this->repository->expects(self::once())->method('ofId')->with($user->getId())->willReturn($user);
        ($this->confirmUserEmailService)($request);
    }

    public function testWhenRequestIsValidThenConfirmUserEmail()
    {
        $request = new ConfirmUserEmailRequest('16bf6c6a-c855-4a36-a3dd-5b9f6d92c753', 'confirmation_token');
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->withConfirmationToken(new Token($request->getConfirmationToken()))
            ->withExpirationDate(new DateTimeImmutable('+1 day'))
            ->build();

        $userAfterUpdate = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->withConfirmationToken(null)
            ->withExpirationDate(null)
            ->withEnabled(true)
            ->build();

        $this->repository->expects(self::once())->method('ofId')->with($user->getId())->willReturn($user);
        $this->repository->expects(self::once())->method('save')->with($this->equalToWithDelta($userAfterUpdate, 1));
        ($this->confirmUserEmailService)($request);
    }
}
