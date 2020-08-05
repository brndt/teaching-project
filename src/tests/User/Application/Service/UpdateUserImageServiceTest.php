<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserImageRequest;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserInformationRequest;
use LaSalle\StudentTeacher\User\Application\Service\UpdateUserImageService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

final class UpdateUserImageServiceTest extends TestCase
{
    private UpdateUserImageService $updateUserImageService;
    private MockObject $repository;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->updateUserImageService = new UpdateUserImageService($this->repository);
    }

    public function testWhenRequestAuthorIsInvalidThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new UpdateUserImageRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753-invalid',
            'cfe849f3-7832-435a-b484-83fabf530794',
            'image.jpg',
        );
        ($this->updateUserImageService)($request);
    }

    public function testWhenRequestAuthorIsNotFoundThenThrowException()
    {
        $this->expectException(UserNotFoundException::class);

        $request = new UpdateUserImageRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'cfe849f3-7832-435a-b484-83fabf530794',
            'image.jpg',
        );
        $this->repository
            ->expects($this->once())
            ->method('ofId')
            ->with(new Uuid($request->getRequestAuthorId()))
            ->willReturn(null);
        ($this->updateUserImageService)($request);
    }

    public function testWhenUserIdIsInvalidThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new UpdateUserImageRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'cfe849f3-7832-435a-b484-83fabf530794-invalid',
            'image.jpg',
        );
        $author = (new UserBuilder())
            ->withId(new Uuid($request->getRequestAuthorId()))
            ->build();
        $this->repository
            ->expects($this->once())
            ->method('ofId')
            ->with($request->getRequestAuthorId())
            ->willReturn($author);
        ($this->updateUserImageService)($request);
    }

    public function testWhenUserIdIsNotFoundThenThrowException()
    {
        $this->expectException(UserNotFoundException::class);

        $request = new UpdateUserImageRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'cfe849f3-7832-435a-b484-83fabf530794',
            'image.jpg',
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
        ($this->updateUserImageService)($request);
    }

    public function testWhenRequestAuthorIsNotUserThanThrowException()
    {
        $this->expectException(PermissionDeniedException::class);

        $request = new UpdateUserImageRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'cfe849f3-7832-435a-b484-83fabf530794',
            'image.jpg',
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
        ($this->updateUserImageService)($request);
    }

    public function testWhenRequestIsValidThenUpdateImage()
    {
        $request = new UpdateUserImageRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753',
            'image.jpg',
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
        ($this->updateUserImageService)($request);
    }

    private function userComparator(User $userExpected): callable
    {
        return function (User $userActual) use ($userExpected) {
            return $userExpected->getImage() === $userActual->getImage();
        };
    }
}
