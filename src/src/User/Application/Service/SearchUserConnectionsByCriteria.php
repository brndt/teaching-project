<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\User\Application\Exception\ConnectionNotFound;
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

    public function __invoke(Criteria $criteria)
    {
        $connections = $this->userConnectionRepository->matching($criteria);
        $this->checkIfExist($connections);
        return new UserConnectionCollectionResponse(...$this->buildResponse(...$connections));
    }

    private function checkIfExist(?array $connections): void
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