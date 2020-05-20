<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\SearchUserCredentialsByIdRequest;
use LaSalle\StudentTeacher\User\Application\Request\SearchUsersByCriteriaRequest;
use LaSalle\StudentTeacher\User\Application\Response\UserCollectionResponse;
use LaSalle\StudentTeacher\User\Application\Response\UserResponse;
use LaSalle\StudentTeacher\User\Application\Service\SearchUsersByCriteriaService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SearchUsersByCriteriaServiceTest extends TestCase
{
    private SearchUsersByCriteriaService $searchUsersByCriteriaService;
    private MockObject $repository;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->searchUsersByCriteriaService = new SearchUsersByCriteriaService($this->repository);
    }

    public function testWhenUsersAreNotFoundThenThrowException()
    {
        $criteria = $this->anyValidCriteria();
        $this->expectException(UserNotFoundException::class);
        $this->repository->expects($this->once())->method('matching')->with(
            $criteria
        )->willReturn([]);
        ($this->searchUsersByCriteriaService)($this->anyValidRequest());
    }

    public function testWhenOneUserIsFoundThenReturnUserCollectionResponse()
    {
        $criteria = $this->anyValidCriteria();
        $this->repository->expects($this->once())->method('matching')->with(
            $criteria
        )->willReturn($this->anyValidArrayOfOneUser());
        $userCollectionResponse = ($this->searchUsersByCriteriaService)($this->anyValidRequest());
        $this->assertEquals($this->userCollectionResponseWithOneUser(), $userCollectionResponse);
    }

    public function testWhenMoreThanOneUserIsFoundThenReturnUserCollectionResponse()
    {
        $criteria = $this->anyValidCriteria();
        $this->repository->expects($this->once())->method('matching')->with(
            $criteria
        )->willReturn($this->anyValidArrayOfManyUsers());
        $userCollectionResponse = ($this->searchUsersByCriteriaService)($this->anyValidRequest());
        $this->assertEquals($this->userCollectionResponseWithManyUsers(), $userCollectionResponse);
    }

    private function anyValidCriteria() {
        return new Criteria(
            Filters::fromValues($this->anyValidRequest()->getFilters()),
            Order::fromValues($this->anyValidRequest()->getOrderBy(), $this->anyValidRequest()->getOrder()),
            Operator::fromValue($this->anyValidRequest()->getOperator()),
            $this->anyValidRequest()->getOffset(),
            $this->anyValidRequest()->getLimit()
        );
    }

    private function userCollectionResponseWithOneUser() {
        return new UserCollectionResponse(...$this->buildResponse(...$this->anyValidArrayOfOneUser()));
    }

    private function userCollectionResponseWithManyUsers() {
        return new UserCollectionResponse(...$this->buildResponse(...$this->anyValidArrayOfManyUsers()));
    }

    private function buildResponse(User ...$users): array
    {
        return array_map(
            static function (User $user) {
                return new UserResponse(
                    $user->getId()->toString(),
                    $user->getFirstName()->toString(),
                    $user->getLastName()->toString(),
                    $user->getRoles()->getArrayOfPrimitives(),
                    $user->getCreated(),
                    $user->getImage(),
                    $user->getExperience(),
                    $user->getEducation(),
                );
            },
            $users
        );
    }

    private function anyValidArrayOfOneUser(): array {
        return [$this->anyValidUser()];
    }

    private function anyValidArrayOfManyUsers(): array {
        return [$this->anyValidUser(), $this->anyValidUser()];
    }

    private function anyValidUser(): User
    {
        return new User(
            new Uuid('16bf6c6a-c855-4a36-a3dd-5b9f6d92c753'),
            new Email('user@example.com'),
            Password::fromHashedPassword('$2y$10$p7s2XiFvYtXIJIfkZxyyMuMUn7/7TDnDBmCXRXOWienN45/oph1we'),
            new Name('Alex'),
            new Name('Johnson'),
            Roles::fromArrayOfPrimitives(['teacher']),
            new \DateTimeImmutable('2020-04-27'),
            false
        );
    }

    private function anyValidRequest() {
        return new SearchUsersByCriteriaRequest(
            [],
            null,
            null,
            null,
            null,
            null
        );
    }
}