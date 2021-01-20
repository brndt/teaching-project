<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Connection\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Connection\Application\Request\SearchUserConnectionsByCriteriaRequest;
use LaSalle\StudentTeacher\User\Connection\Application\Response\UserConnectionCollectionResponse;
use LaSalle\StudentTeacher\User\Connection\Application\Response\UserConnectionResponse;
use LaSalle\StudentTeacher\User\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Connection\Domain\Aggregate\UserConnection;
use LaSalle\StudentTeacher\User\Connection\Domain\Repository\UserConnectionRepository;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Connection\Domain\Service\UserConnectionService;
use LaSalle\StudentTeacher\User\User\Domain\Service\UserService;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Role;

final class SearchUserConnectionsByCriteriaService
{
    private UserService $userService;
    private UserConnectionService $userConnectionService;

    public function __construct(
        private UserConnectionRepository $userConnectionRepository,
        private UserRepository $userRepository,
        private AuthorizationService $authorizationService
    ) {
        $this->userService = new UserService($this->userRepository);
        $this->userConnectionService = new UserConnectionService($this->userConnectionRepository);
    }

    public function __invoke(SearchUserConnectionsByCriteriaRequest $request)
    {
        $authorId = new Uuid($request->getRequestAuthorId());
        $author = $this->userService->findUser($authorId);

        $userId = new Uuid($request->getUserId());
        $user = $this->userService->findUser($userId);

        $this->authorizationService->ensureRequestAuthorHasPermissionsToUserConnection($author, $user);

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

    private function createFiltersByUserId(User $user): array
    {
        if (true === $user->isInRole(new Role(Role::STUDENT))) {
            return [['field' => 'studentId', 'operator' => '=', 'value' => $user->getId()->toString()]];
        }
        return [['field' => 'teacherId', 'operator' => '=', 'value' => $user->getId()->toString()]];
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
