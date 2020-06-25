<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserInformationRequest;
use LaSalle\StudentTeacher\User\Application\Service\UpdateUserInformationService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class UpdateUserInformationServiceTest extends TestCase
{
    private UpdateUserInformationService $updateUserInformationService;
    private MockObject $repository;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->updateUserInformationService = new UpdateUserInformationService($this->repository);
    }

    public function testWhenRequestAuthorIsInvalidThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new UpdateUserInformationRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753-invalid',
            'cfe849f3-7832-435a-b484-83fabf530794',
            'example@mail.com',
            'Alex',
            'Johnsson',
            '10 years',
            'la salle'
        );
        ($this->updateUserInformationService)($request);
    }

    public function testWhenRequestAuthorIsNotFoundThenThrowException()
    {
        $this->expectException(UserNotFoundException::class);

        $request = new UpdateUserInformationRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'cfe849f3-7832-435a-b484-83fabf530794',
            'example@mail.com',
            'Alex',
            'Johnsson',
            '10 years',
            'la salle'
        );
        $this->repository
            ->expects($this->once())
            ->method('ofId')
            ->with(new Uuid($request->getRequestAuthorId()))
            ->willReturn(null);
        ($this->updateUserInformationService)($request);
    }

    public function testWhenUserIdIsInvalidThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new UpdateUserInformationRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'cfe849f3-7832-435a-b484-83fabf530794-invalid',
            'example@mail.com',
            'Alex',
            'Johnsson',
            '10 years',
            'la salle'
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $this->repository
            ->expects($this->once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        ($this->updateUserInformationService)($request);
    }

    public function testWhenUserIdIsNotFoundThenThrowException()
    {
        $this->expectException(UserNotFoundException::class);

        $request = new UpdateUserInformationRequest(
            'cfe849f3-7832-435a-b484-83fabf530794',
            'cfe849f3-7832-435a-b484-83fabf530794',
            'example@mail.com',
            'Alex',
            'Johnsson',
            '10 years',
            'la salle'
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
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
            ->willReturn(null);
        ($this->updateUserInformationService)($request);
    }

    public function testWhenRequestAuthorIsNotUserThanThrowException()
    {
        $this->expectException(PermissionDeniedException::class);

        $request = new UpdateUserInformationRequest(
            'cfe849f3-7832-435a-b484-83fabf530794',
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'example@mail.com',
            'Alex',
            'Johnsson',
            '10 years',
            'la salle'
        );
        $author = (new UserBuilder())->build();
        $user = (new UserBuilder())->build();
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
        ($this->updateUserInformationService)($request);
    }

    public function testWhenUserEmailIsInvalidThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new UpdateUserInformationRequest(
            'cfe849f3-7832-435a-b484-83fabf530794',
            'cfe849f3-7832-435a-b484-83fabf530794',
            'examplemail.com',
            'Alex',
            'Johnsson',
            '10 years',
            'la salle'
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
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
        ($this->updateUserInformationService)($request);
    }

    public function testWhenUserEmailIsNotAvailableThenThrowException()
    {
        $this->expectException(UserAlreadyExistsException::class);

        $request = new UpdateUserInformationRequest(
            'cfe849f3-7832-435a-b484-83fabf530794',
            'cfe849f3-7832-435a-b484-83fabf530794',
            'notavailable@example.com',
            'Alex',
            'Johnsson',
            '10 years',
            'la salle'
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->build();
        $userToSearchEmail = (new UserBuilder())
            ->withEmail(new Email($request->getEmail()))
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
            ->method('ofEmail')
            ->with($user->getEmail())
            ->willReturn($userToSearchEmail);
        ($this->updateUserInformationService)($request);
    }

    public function testWhenUserFirstNameIsInvalidThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new UpdateUserInformationRequest(
            'cfe849f3-7832-435a-b484-83fabf530794',
            'cfe849f3-7832-435a-b484-83fabf530794',
            'example@mail.com',
            'Alex i$$$',
            'Johnsson',
            '10 years',
            'la salle'
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
            ->build();
        $this->repository->expects($this->at(0))->method('ofId')->with(
            $request->getRequestAuthorId()
        )->willReturn($author);
        $this->repository->expects($this->at(1))->method('ofId')->with(
            $request->getUserId()
        )->willReturn($user);
        ($this->updateUserInformationService)($request);
    }

    public function testWhenUserLastNameIsInvalidThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new UpdateUserInformationRequest(
            'cfe849f3-7832-435a-b484-83fabf530794',
            'cfe849f3-7832-435a-b484-83fabf530794',
            'example@mail.com',
            'Alex',
            'Johnsson  ',
            '10 years',
            'la salle'
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
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
        ($this->updateUserInformationService)($request);
    }

    public function testWhenRequestIsValidThenUpdateInformation()
    {
        $request = new UpdateUserInformationRequest(
            'cfe849f3-7832-435a-b484-83fabf530794',
            'cfe849f3-7832-435a-b484-83fabf530794',
            'example@mail.com',
            'Alex',
            'Johnsson',
            '10 years',
            'la salle'
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $user = (new UserBuilder())
            ->withId(new Uuid($request->getUserId()))
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
        $this->repository->expects($this->once())->method('save')->with(
            $this->callback($this->userComparator($user))
        );
        ($this->updateUserInformationService)($request);
    }

    private function userComparator(User $userExpected): callable
    {
        return function (User $userActual) use ($userExpected) {
            return $userExpected->getFirstName()->toString() === $userActual->getFirstName()->toString()
                && $userExpected->getEmail()->toString() === $userActual->getEmail()->toString()
                && $userExpected->getFirstName()->toString() === $userActual->getFirstName()->toString()
                && $userExpected->getLastName()->toString() === $userActual->getLastName()->toString()
                && $userExpected->getExperience() === $userActual->getExperience()
                && $userExpected->getEducation() === $userActual->getEducation();
        };
    }
}
