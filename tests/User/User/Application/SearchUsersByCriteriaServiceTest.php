<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\User\Application;

use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\User\Shared\Application\Request\SearchUsersByCriteriaRequest;
use LaSalle\StudentTeacher\User\Shared\Application\Response\UserCollectionResponse;
use LaSalle\StudentTeacher\User\Shared\Application\Response\UserResponse;
use LaSalle\StudentTeacher\User\Shared\Application\Service\SearchUsersByCriteriaService;
use LaSalle\StudentTeacher\User\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\User\Domain\UserBuilder;

final class SearchUsersByCriteriaServiceTest extends TestCase
{
    private SearchUsersByCriteriaService $searchUsersByCriteriaService;
    private MockObject $repository;

    public function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->searchUsersByCriteriaService = new SearchUsersByCriteriaService($this->repository);
    }

    public function testWhenUsersAreNotFoundThenReturnEmptyArray()
    {
        $request = new SearchUsersByCriteriaRequest([], null, null, null, null, null);
        $expectedUserCollectionResponse = new UserCollectionResponse(
            ...$this->buildResponse(...[])
        );
        $criteria = new Criteria(
            Filters::fromValues($request->getFilters()),
            Order::fromValues($request->getOrderBy(), $request->getOrder()),
            Operator::fromValue($request->getOperator()),
            $request->getOffset(),
            $request->getLimit()
        );
        $this->repository->expects(self::once())->method('matching')->with($criteria)->willReturn([]);
        $userCollectionResponse = ($this->searchUsersByCriteriaService)($request);
        $this->assertEquals($expectedUserCollectionResponse, $userCollectionResponse);
    }

    public function testWhenOneUserIsFoundThenReturnUserCollectionResponse()
    {
        $request = new SearchUsersByCriteriaRequest([], null, null, null, null, null);
        $criteria = new Criteria(
            Filters::fromValues($request->getFilters()),
            Order::fromValues($request->getOrderBy(), $request->getOrder()),
            Operator::fromValue($request->getOperator()),
            $request->getOffset(),
            $request->getLimit()
        );
        $arrayOfOneUser[] = (new UserBuilder())->build();
        $userCollectionResponseWithOneUser = new UserCollectionResponse(...$this->buildResponse(...$arrayOfOneUser));

        $this->repository->expects(self::once())->method('matching')->with($criteria)->willReturn($arrayOfOneUser);
        $userCollectionResponse = ($this->searchUsersByCriteriaService)($request);
        $this->assertEquals($userCollectionResponseWithOneUser, $userCollectionResponse);
    }

    public function testWhenMoreThanOneUserIsFoundThenReturnUserCollectionResponse()
    {
        $request = new SearchUsersByCriteriaRequest([], null, null, null, null, null);
        $criteria = new Criteria(
            Filters::fromValues($request->getFilters()),
            Order::fromValues($request->getOrderBy(), $request->getOrder()),
            Operator::fromValue($request->getOperator()),
            $request->getOffset(),
            $request->getLimit()
        );
        $arrayOfManyUsers[] = (new UserBuilder())->build();
        $arrayOfManyUsers[] = (new UserBuilder())->build();
        $userCollectionResponseWithManyUsers = new UserCollectionResponse(
            ...$this->buildResponse(...$arrayOfManyUsers)
        );

        $this->repository->expects(self::once())->method('matching')->with($criteria)->willReturn($arrayOfManyUsers);
        $userCollectionResponse = ($this->searchUsersByCriteriaService)($request);
        $this->assertEquals($userCollectionResponseWithManyUsers, $userCollectionResponse);
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
}
