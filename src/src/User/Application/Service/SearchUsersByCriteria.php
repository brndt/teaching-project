<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\User\Application\Request\SearchUsersByCriteriaRequest;
use LaSalle\StudentTeacher\User\Application\Response\UserCollectionResponse;
use LaSalle\StudentTeacher\User\Application\Response\UserResponse;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;

final class SearchUsersByCriteria extends UserService
{
    public function __invoke(SearchUsersByCriteriaRequest $request): UserCollectionResponse
    {
        $criteria = new Criteria(
            Filters::fromValues($request->getFilters()),
            Order::fromValues($request->getOrderBy(), $request->getOrder()),
            Operator::fromValue($request->getOperator()),
            $request->getOffset(),
            $request->getLimit()
        );

        $users = $this->userRepository->matching($criteria);
        $this->ensureUsersExist($users);
        return new UserCollectionResponse(...$this->buildResponse(...$users));
    }

    private function buildResponse(User ...$users): array
    {
        return array_map(
            static function (User $user) {
                return new UserResponse(
                    $user->getId()->toString(),
                    $user->getFirstName(),
                    $user->getLastName(),
                    $user->getRoles()->getArrayOfPrimitives(),
                    $user->getCreated()->format('Y-m-d H:i:s'),
                    $user->getImage(),
                    $user->getExperience(),
                    $user->getEducation(),
                );
            },
            $users
        );
    }
}