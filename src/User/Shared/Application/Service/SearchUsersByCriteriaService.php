<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Shared\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\User\Shared\Application\Request\SearchUsersByCriteriaRequest;
use LaSalle\StudentTeacher\User\Shared\Application\Response\UserCollectionResponse;
use LaSalle\StudentTeacher\User\Shared\Application\Response\UserResponse;
use LaSalle\StudentTeacher\User\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;

final class SearchUsersByCriteriaService
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function __invoke(SearchUsersByCriteriaRequest $request): UserCollectionResponse
    {
        $criteria = new Criteria(
            Filters::fromValues($request->getFilters()),
            Order::fromValues($request->getOrderBy(), $request->getOrder()),
            Operator::fromValue($request->getOperator()),
            $request->getOffset(),
            $request->getLimit()
        );

        $users = $this->repository->matching($criteria);

        return new UserCollectionResponse(...$this->buildResponse(...$users));
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
