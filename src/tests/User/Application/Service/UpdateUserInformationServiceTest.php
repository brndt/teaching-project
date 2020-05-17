<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserInformationRequest;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserPasswordRequest;
use LaSalle\StudentTeacher\User\Application\Service\UpdateUserInformationService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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
        ($this->updateUserInformationService)($this->anyUpdateUserInformationRequestWithInvalidRequestAuthorId());
    }

    public function testWhenRequestAuthorIsNotFoundThenThrowException()
    {
        $this->expectException(UserNotFoundException::class);
        $this->repository->expects($this->once())->method('ofId')->with(
            $this->anyValidUpdateUserInformationRequest()->getRequestAuthorId()
        )->willReturn(null);
        ($this->updateUserInformationService)($this->anyValidUpdateUserInformationRequest());
    }

    public function testWhenUserIdIsInvalidThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->repository->expects($this->once())->method('ofId')->with(
            $this->anyUpdateUserInformationRequestWithInvalidRequestUserId()->getRequestAuthorId()
        )->willReturn($this->anyValidAuthor());
        ($this->updateUserInformationService)($this->anyUpdateUserInformationRequestWithInvalidRequestUserId());
    }

    public function testWhenUserIdIsNotFoundThenThrowException()
    {
        $this->expectException(UserNotFoundException::class);
        $this->repository->expects($this->at(0))->method('ofId')->with(
            $this->anyValidUpdateUserInformationRequest()->getRequestAuthorId()
        )->willReturn($this->anyValidAuthor());
        $this->repository->expects($this->at(1))->method('ofId')->with(
            $this->anyValidUpdateUserInformationRequest()->getUserId()
        )->willReturn(null);
        ($this->updateUserInformationService)($this->anyValidUpdateUserInformationRequest());
    }

    public function testWhenRequestAuthorIsNotUserThanThrowException()
    {
        $this->expectException(PermissionDeniedException::class);
        $this->repository->expects($this->at(0))->method('ofId')->with(
            $this->anyUpdateUserInformationRequestWithDifferentAuthorAndUser()->getRequestAuthorId()
        )->willReturn($this->anyValidAuthor());
        $this->repository->expects($this->at(1))->method('ofId')->with(
            $this->anyUpdateUserInformationRequestWithDifferentAuthorAndUser()->getUserId()
        )->willReturn($this->anyValidUser());
        ($this->updateUserInformationService)($this->anyUpdateUserInformationRequestWithDifferentAuthorAndUser());
    }

    public function testWhenUserEmailIsInvalidThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->repository->expects($this->at(0))->method('ofId')->with(
            $this->anyUpdateUserInformationRequestWithInvalidEmail()->getRequestAuthorId()
        )->willReturn($this->anyValidUser());
        $this->repository->expects($this->at(1))->method('ofId')->with(
            $this->anyUpdateUserInformationRequestWithInvalidEmail()->getUserId()
        )->willReturn($this->anyValidUser());
        ($this->updateUserInformationService)($this->anyUpdateUserInformationRequestWithInvalidEmail());
    }

    public function testWhenUserEmailIsNotAvailableThenThrowException()
    {
        $this->expectException(UserAlreadyExistsException::class);
        $this->repository->expects($this->at(0))->method('ofId')->with(
            $this->anyUpdateUserInformationRequestWithNotAvailableEmail()->getRequestAuthorId()
        )->willReturn($this->anyValidUser());
        $this->repository->expects($this->at(1))->method('ofId')->with(
            $this->anyUpdateUserInformationRequestWithNotAvailableEmail()->getUserId()
        )->willReturn($this->anyValidUser());
        $userToSearchEmail = $this->anyValidUser();
        $userToSearchEmail->setEmail(new Email($this->anyUpdateUserInformationRequestWithNotAvailableEmail()->getEmail()));
        $this->repository->expects($this->at(2))->method('ofEmail')->with(
            $this->anyValidUser()->getEmail()
        )->willReturn($userToSearchEmail);
        ($this->updateUserInformationService)($this->anyUpdateUserInformationRequestWithNotAvailableEmail());
    }

    public function testWhenUserFirstNameIsInvalidThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->repository->expects($this->at(0))->method('ofId')->with(
            $this->anyUpdateUserInformationRequestWithInvalidFirstName()->getRequestAuthorId()
        )->willReturn($this->anyValidUser());
        $this->repository->expects($this->at(1))->method('ofId')->with(
            $this->anyUpdateUserInformationRequestWithInvalidFirstName()->getUserId()
        )->willReturn($this->anyValidUser());
        ($this->updateUserInformationService)($this->anyUpdateUserInformationRequestWithInvalidFirstName());
    }

    public function testWhenUserLastNameIsInvalidThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->repository->expects($this->at(0))->method('ofId')->with(
            $this->anyUpdateUserInformationRequestWithInvalidLastName()->getRequestAuthorId()
        )->willReturn($this->anyValidUser());
        $this->repository->expects($this->at(1))->method('ofId')->with(
            $this->anyUpdateUserInformationRequestWithInvalidLastName()->getUserId()
        )->willReturn($this->anyValidUser());
        ($this->updateUserInformationService)($this->anyUpdateUserInformationRequestWithInvalidLastName());
    }

    public function testWhenRequestIsValidThenUpdateInformation()
    {
        $this->repository->expects($this->at(0))->method('ofId')->with(
            $this->anyValidUpdateUserInformationRequest()->getRequestAuthorId()
        )->willReturn($this->anyValidUser());
        $this->repository->expects($this->at(1))->method('ofId')->with(
            $this->anyValidUpdateUserInformationRequest()->getUserId()
        )->willReturn($this->anyValidUser());

        $userToUpdate = $this->anyValidUser();
        $userToUpdate->setEmail(new Email($this->anyValidUpdateUserInformationRequest()->getEmail()));
        $userToUpdate->setFirstName(new Name($this->anyValidUpdateUserInformationRequest()->getFirstName()));
        $userToUpdate->setLastName(new Name($this->anyValidUpdateUserInformationRequest()->getLastName()));
        $userToUpdate->setImage($this->anyValidUpdateUserInformationRequest()->getImage());
        $userToUpdate->setEducation($this->anyValidUpdateUserInformationRequest()->getEducation());
        $userToUpdate->setExperience($this->anyValidUpdateUserInformationRequest()->getExperience());

        $this->repository->expects($this->once())->method('save')->with($this->callback($this->userComparator($userToUpdate)));
        ($this->updateUserInformationService)($this->anyValidUpdateUserInformationRequest());
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

    private function anyUpdateUserInformationRequestWithInvalidRequestAuthorId(): UpdateUserInformationRequest
    {
        return new UpdateUserInformationRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753-invalid',
            'cfe849f3-7832-435a-b484-83fabf530794',
            'example@mail.com',
            'Alex',
            'Johnsson',
            'image.jpg',
            '10 years',
            'la salle'
        );
    }

    private function anyUpdateUserInformationRequestWithInvalidRequestUserId(): UpdateUserInformationRequest
    {
        return new UpdateUserInformationRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'cfe849f3-7832-435a-b484-83fabf530794-invalid',
            'example@mail.com',
            'Alex',
            'Johnsson',
            'image.jpg',
            '10 years',
            'la salle'
        );
    }

    private function anyUpdateUserInformationRequestWithInvalidEmail(): UpdateUserInformationRequest
    {
        return new UpdateUserInformationRequest(
            'cfe849f3-7832-435a-b484-83fabf530794',
            'cfe849f3-7832-435a-b484-83fabf530794',
            'examplemail.com',
            'Alex',
            'Johnsson',
            'image.jpg',
            '10 years',
            'la salle'
        );
    }

    private function anyUpdateUserInformationRequestWithNotAvailableEmail(): UpdateUserInformationRequest
    {
        return new UpdateUserInformationRequest(
            'cfe849f3-7832-435a-b484-83fabf530794',
            'cfe849f3-7832-435a-b484-83fabf530794',
            'notavailable@example.com',
            'Alex',
            'Johnsson',
            'image.jpg',
            '10 years',
            'la salle'
        );
    }


    private function anyUpdateUserInformationRequestWithInvalidFirstName(): UpdateUserInformationRequest
    {
        return new UpdateUserInformationRequest(
            'cfe849f3-7832-435a-b484-83fabf530794',
            'cfe849f3-7832-435a-b484-83fabf530794',
            'example@mail.com',
            'Alex i$$$',
            'Johnsson',
            'image.jpg',
            '10 years',
            'la salle'
        );
    }

    private function anyUpdateUserInformationRequestWithInvalidLastName(): UpdateUserInformationRequest
    {
        return new UpdateUserInformationRequest(
            'cfe849f3-7832-435a-b484-83fabf530794',
            'cfe849f3-7832-435a-b484-83fabf530794',
            'example@mail.com',
            'Alex',
            'Johnsson  ',
            'image.jpg',
            '10 years',
            'la salle'
        );
    }

    private function anyUpdateUserInformationRequestWithDifferentAuthorAndUser(): UpdateUserInformationRequest
    {
        return new UpdateUserInformationRequest(
            'cfe849f3-7832-435a-b484-83fabf530794',
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'example@mail.com',
            'Alex',
            'Johnsson',
            'image.jpg',
            '10 years',
            'la salle'
        );
    }

    private function anyValidUpdateUserInformationRequest(): UpdateUserInformationRequest
    {
        return new UpdateUserInformationRequest(
            'cfe849f3-7832-435a-b484-83fabf530794',
            'cfe849f3-7832-435a-b484-83fabf530794',
            'example@mail.com',
            'Alex',
            'Johnsson',
            'image.jpg',
            '10 years',
            'la salle'
        );
    }

    private function userComparator(User $userExpected): callable
    {
        return function (User $userActual) use ($userExpected) {
            return $userExpected->getEmail()->toString() === $userActual->getEmail()->toString()
                && $userExpected->getFirstName()->toString() === $userActual->getFirstName()->toString()
                && $userExpected->getLastName()->toString() === $userActual->getLastName()->toString()
                && $userExpected->getRoles()->getArrayOfPrimitives() === $userActual->getRoles()->getArrayOfPrimitives()
                && $userExpected->getImage() === $userActual->getImage()
                && $userExpected->getExperience() === $userActual->getExperience()
                && $userExpected->getEducation() === $userActual->getEducation()
                && $userExpected->getCreated() == $userActual->getCreated();
        };
    }
}