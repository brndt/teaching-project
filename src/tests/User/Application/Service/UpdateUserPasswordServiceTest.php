<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\IncorrectPasswordException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserPasswordRequest;
use LaSalle\StudentTeacher\User\Application\Service\UpdateUserPasswordService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class UpdateUserPasswordServiceTest extends TestCase
{
    private UpdateUserPasswordService $updateUserPasswordService;
    private MockObject $repository;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->updateUserPasswordService = new UpdateUserPasswordService($this->repository);
    }

    public function testWhenRequestAuthorIsInvalidThenThrowException()
    {
        $request = new UpdateUserPasswordRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753-invalid',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '123456aa',
            'qwerty123'
        );

        $this->expectException(InvalidArgumentException::class);
        ($this->updateUserPasswordService)($request);
    }

    public function testWhenRequestAuthorIsNotFoundThenThrowException()
    {
        $request = new UpdateUserPasswordRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            '123456aa',
            'qwerty123'
        );

        $this->expectException(UserNotFoundException::class);
        $this->repository
            ->expects($this->once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn(null);
        ($this->updateUserPasswordService)($request);
    }

    public function testWhenUserIdIsInvalidThenThrowException()
    {
        $request = new UpdateUserPasswordRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'cfe849f3-7832-435a-b484-83fabf530794-invalid',
            'qwerty12',
            'qwerty123'
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $this->expectException(InvalidArgumentException::class);
        $this->repository
            ->expects($this->once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        ($this->updateUserPasswordService)($request);
    }

    public function testWhenUserIdIsNotFoundThenThrowException()
    {
        $request = new UpdateUserPasswordRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            '123456aa',
            'qwerty123'
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();

        $this->expectException(UserNotFoundException::class);
        $this->repository
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        $this->repository
            ->expects($this->at(1))
            ->method('ofId')
            ->with($request->getUserId())
            ->willReturn(null);
        ($this->updateUserPasswordService)($request);
    }

    public function testWhenOldPasswordIsNotCorrectThanThrowException()
    {
        $request = new UpdateUserPasswordRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'incorrectpassword123',
            'qwerty123'
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->build();

        $this->expectException(IncorrectPasswordException::class);
        $this->repository
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        $this->repository
            ->expects($this->at(1))
            ->method('ofId')
            ->with($request->getUserId())
            ->willReturn($user);
        ($this->updateUserPasswordService)($request);
    }

    public function testWhenRequestAuthorIsNotUserThanThrowException()
    {
        $request = new UpdateUserPasswordRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '123456aa',
            'qwerty123'
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->build();

        $this->expectException(PermissionDeniedException::class);
        $this->repository
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        $this->repository
            ->expects($this->at(1))
            ->method('ofId')
            ->with($request->getUserId())
            ->willReturn($user);
        ($this->updateUserPasswordService)($request);
    }

    public function testWhenRequestIsValidThenUpdatePassword()
    {
        $request = new UpdateUserPasswordRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            '123456aa',
            'qwerty123'
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->build();

        $expectedUserToUpdate = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->withPassword(Password::fromPlainPassword($request->getNewPassword()))
            ->build();

        $this->repository
            ->expects($this->at(0))
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        $this->repository
            ->expects($this->at(1))
            ->method('ofId')
            ->with($request->getUserId())
            ->willReturn($user);
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback($this->userComparator($expectedUserToUpdate, $request->getNewPassword())));
        $this->assertNull(($this->updateUserPasswordService)($request));
    }

    private function userComparator(User $userExpected, string $plainPassword): callable
    {
        return function (User $userActual) use ($userExpected, $plainPassword) {
            return $userExpected->getId()->toString() === $userActual->getId()->toString()
                && password_verify($plainPassword, $userActual->getPassword()->toString());
        };
    }
}