<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\User\Application\Request\SearchUserConnectionsByCriteriaRequest;
use LaSalle\StudentTeacher\User\Application\Response\UserConnectionCollectionResponse;
use LaSalle\StudentTeacher\User\Application\Response\UserConnectionResponse;
use LaSalle\StudentTeacher\User\Domain\Aggregate\UserConnection;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;

final class SearchUserConnectionsByCriteria extends UserConnectionService
{
    public function __invoke(SearchUserConnectionsByCriteriaRequest $request)
    {
        $user = $this->identifyUserById($request->getUserId());

        $criteria = new Criteria(
            Filters::fromValues($this->createFiltersByUserId($user)),
            Order::fromValues($request->getOrderBy(), $request->getOrder()),
            Operator::fromValue($request->getOperator()),
            $request->getOffset(),
            $request->getLimit()
        );

        $connections = $this->userConnectionRepository->matching($criteria);
        $this->ensureConnectionsExist($connections);

        $buildResponse = Role::STUDENT === $this->verifyRole($user) ?
            $this->buildStudentResponse(...$connections) :
            $this->buildTeacherResponse();

        return new UserConnectionCollectionResponse(...$buildResponse);
    }

    private function buildStudentResponse(UserConnection ...$connections): array
    {
        return array_map(
            static function (UserConnection $connection) {
                return new UserConnectionResponse(
                    $connection->getStudentId()->toString(),
                    $connection->getTeacherId()->toString(),
                    (string)$connection->getState(),
                    $connection->getSpecifierId()->toString()
                );
            },
            $connections
        );
    }

    private function buildTeacherResponse(UserConnection ...$connections): array
    {
        return array_map(
            static function (UserConnection $connection) {
                return new UserConnectionResponse(
                    $connection->getTeacherId()->toString(),
                    $connection->getStudentId()->toString(),
                    (string)$connection->getState(),
                    $connection->getSpecifierId()->toString()
                );
            },
            $connections
        );
    }
}