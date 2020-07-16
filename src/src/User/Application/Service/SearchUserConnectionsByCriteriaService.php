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

final class SearchUserConnectionsByCriteriaService extends UserConnectionService
{
    public function __invoke(SearchUserConnectionsByCriteriaRequest $request)
    {
        $authorId = $this->createIdFromPrimitive($request->getRequestAuthorId());
        $author = $this->userRepository->ofId($authorId);
        $this->ensureUserExists($author);

        $userId = $this->createIdFromPrimitive($request->getUserId());
        $user = $this->userRepository->ofId($userId);
        $this->ensureUserExists($user);

        $this->ensureRequestAuthorHasPermissions($author, $user);

        $criteria = new Criteria(
            Filters::fromValues($this->createFiltersByUserId($user)),
            Order::fromValues($request->getOrderBy(), $request->getOrder()),
            Operator::fromValue($request->getOperator()),
            $request->getOffset(),
            $request->getLimit()
        );

        $connections = $this->userConnectionRepository->matching($criteria);

        if (true === $user->isInRole(new Role(Role::STUDENT))) {
            return new UserConnectionCollectionResponse(...$this->buildStudentResponse(...$connections));
        }

        return new UserConnectionCollectionResponse(...$this->buildTeacherResponse(...$connections));
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
