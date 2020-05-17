<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\IncorrectPasswordException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotEnabledException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserPasswordRequest;
use LaSalle\StudentTeacher\User\Application\Service\UpdateUserPasswordService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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
        $this->expectException(InvalidArgumentException::class);
        ($this->updateUserPasswordService)($this->anyUpdateUserPasswordRequestWithInvalidRequestAuthorId());
    }

    public function testWhenRequestAuthorIsNotFoundThenThrowException()
    {
        $this->expectException(UserNotFoundException::class);
        $this->repository->expects($this->once())->method('ofId')->with(
            $this->anyUpdateUserPasswordRequest()->getRequestAuthorId()
        )->willReturn(null);
        ($this->updateUserPasswordService)($this->anyUpdateUserPasswordRequest());
    }

    public function testWhenUserIdIsInvalidThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->repository->expects($this->once())->method('ofId')->with(
            $this->anyUpdateUserPasswordRequestWithInvalidRequestUserId()->getRequestAuthorId()
        )->willReturn($this->anyValidAuthor());
        ($this->updateUserPasswordService)($this->anyUpdateUserPasswordRequestWithInvalidRequestUserId());
    }

    public function testWhenUserIdIsNotFoundThenThrowException()
    {
        $this->expectException(UserNotFoundException::class);
        $this->repository->expects($this->at(0))->method('ofId')->with(
            $this->anyUpdateUserPasswordRequest()->getRequestAuthorId()
        )->willReturn($this->anyValidAuthor());
        $this->repository->expects($this->at(1))->method('ofId')->with(
            $this->anyUpdateUserPasswordRequest()->getUserId()
        )->willReturn(null);
        ($this->updateUserPasswordService)($this->anyUpdateUserPasswordRequest());
    }

    public function testWhenOldPasswordIsNotCorrectThanThrowException()
    {
        $this->expectException(IncorrectPasswordException::class);
        $this->repository->expects($this->at(0))->method('ofId')->with(
            $this->anyUpdateUserPasswordRequestWithIncorrectPassword()->getRequestAuthorId()
        )->willReturn($this->anyValidUser());
        $this->repository->expects($this->at(1))->method('ofId')->with(
            $this->anyUpdateUserPasswordRequestWithIncorrectPassword()->getUserId()
        )->willReturn($this->anyValidUser());
        ($this->updateUserPasswordService)($this->anyUpdateUserPasswordRequestWithIncorrectPassword());
    }

    public function testWhenRequestAuthorIsNotUserThanThrowException()
    {
        $this->expectException(PermissionDeniedException::class);
        $this->repository->expects($this->at(0))->method('ofId')->with(
            $this->anyUpdateUserPasswordRequest()->getRequestAuthorId()
        )->willReturn($this->anyValidAuthor());
        $this->repository->expects($this->at(1))->method('ofId')->with(
            $this->anyUpdateUserPasswordRequest()->getUserId()
        )->willReturn($this->anyValidUser());
        ($this->updateUserPasswordService)($this->anyUpdateUserPasswordRequest());
    }

    public function testWhenRequestIsValidThenUpdatePassword()
    {
        $this->repository->expects($this->at(0))->method('ofId')->with(
            $this->anyUpdateUserPasswordRequest()->getRequestAuthorId()
        )->willReturn($this->anyValidUser());
        $this->repository->expects($this->at(1))->method('ofId')->with(
            $this->anyUpdateUserPasswordRequest()->getUserId()
        )->willReturn($this->anyValidUser());
        $expectedUserToUpdate = $this->anyValidUser();
        $expectedUserToUpdate->setPassword(Password::fromPlainPassword($this->anyUpdateUserPasswordRequest()->getNewPassword()));
        $this->repository->expects($this->once())->method('save')->with($this->callback($this->userComparator($expectedUserToUpdate)));
        $this->assertNull(($this->updateUserPasswordService)($this->anyUpdateUserPasswordRequest()));
    }

    private function anyUpdateUserPasswordRequest(): UpdateUserPasswordRequest
    {
        return new UpdateUserPasswordRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            '123456aa',
            'qwerty123'
        );
    }

    private function anyUpdateUserPasswordRequestWithIncorrectPassword(): UpdateUserPasswordRequest
    {
        return new UpdateUserPasswordRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'incorrectpassword123',
            'qwerty123'
        );
    }

    private function anyUpdateUserPasswordRequestWithInvalidRequestAuthorId(): UpdateUserPasswordRequest
    {
        return new UpdateUserPasswordRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753-invalid',
            'cfe849f3-7832-435a-b484-83fabf530794',
            '123456aa',
            'qwerty123'
        );
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

    private function anyValidAuthor(): User
    {
        return new User(
            new Uuid('cfe849f3-7832-435a-b484-83fabf530794'),
            new Email('user@example.com'),
            Password::fromPlainPassword('123456aa'),
            new Name('Alex'),
            new Name('Johnson'),
            Roles::fromArrayOfPrimitives(['teacher']),
            new \DateTimeImmutable('2020-04-27'),
            false
        );
    }

    private function anyUpdateUserPasswordRequestWithInvalidRequestUserId(): UpdateUserPasswordRequest
    {
        return new UpdateUserPasswordRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'cfe849f3-7832-435a-b484-83fabf530794-invalid',
            'qwerty12',
            'qwerty123'
        );
    }

    private function userComparator(User $userExpected): callable
    {
        return function (User $userActual) use ($userExpected) {
            return $userExpected->getEmail()->toString() === $userActual->getEmail()->toString()
                && $userExpected->getFirstName()->toString() === $userActual->getFirstName()->toString()
                && $userExpected->getLastName()->toString() === $userActual->getLastName()->toString()
                && $userExpected->getRoles()->getArrayOfPrimitives() === $userActual->getRoles()->getArrayOfPrimitives()
                && $userExpected->getCreated() == $userActual->getCreated();
        };
    }
}