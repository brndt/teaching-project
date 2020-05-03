<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\User\Application\Exception\ConnectionNotFound;
use LaSalle\StudentTeacher\User\Application\Request\SearchUserConnectionsByCriteriaRequest;
use LaSalle\StudentTeacher\User\Application\Response\UserConnectionCollectionResponse;
use LaSalle\StudentTeacher\User\Application\Response\UserConnectionResponse;
use LaSalle\StudentTeacher\User\Domain\Aggregate\UserConnection;
use LaSalle\StudentTeacher\User\Domain\Repository\UserConnectionRepository;

final class SearchUserConnectionsByCriteria
{
    private UserConnectionRepository $userConnectionRepository;

    public function __construct(UserConnectionRepository $userConnectionRepository)
    {
        $this->userConnectionRepository = $userConnectionRepository;
    }

    public function __invoke(SearchUserConnectionsByCriteriaRequest $request)
    {
        $this->ensureRequestAuthorCanExecute($request->getRequestAuthorId(), $request->getUserId());

        $criteria = new Criteria(
            Filters::fromValues($this->createFiltersByUserId($request->getUserId())),
            Order::fromValues($request->getOrderBy(), $request->getOrder()),
            Operator::fromValue($request->getOperator()),
            $request->getOffset(),
            $request->getLimit()
        );

        $connections = $this->userConnectionRepository->matching($criteria);
        $this->checkIfConnectionsExist($connections);

        return new UserConnectionCollectionResponse(...$this->buildResponse(...$connections));
    }

    private function createFiltersByUserId(string $userId): array {
        return [
            ['field' => 'userId', 'operator' => '=', 'value' => $userId],
            ['field' => 'friendId', 'operator' => '=', 'value' => $userId]
        ];
    }

    private function ensureRequestAuthorCanExecute(string $requestAuthorId, string $userId): void
    {
        if ($requestAuthorId !== $userId) {
            throw new PermissionDeniedException();
        }
    }

    private function checkIfConnectionsExist(?array $connections): void
    {
        if (true === empty($connections)) {
            throw new ConnectionNotFound();
        }
    }

    private function buildResponse(UserConnection ...$connections): array
    {
        return array_map(
            static function (UserConnection $connection) {
                return new UserConnectionResponse(
                    $connection->getUserId()->toString(),
                    $connection->getFriendId()->toString(),
                    $connection->getStatus()->toString(),
                );
            },
            $connections
        );
    }
}