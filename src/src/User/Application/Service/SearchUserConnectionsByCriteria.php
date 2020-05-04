<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\ConnectionNotFound;
use LaSalle\StudentTeacher\User\Application\Exception\RoleIsNotStudentOrTeacherException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\SearchUserConnectionsByCriteriaRequest;
use LaSalle\StudentTeacher\User\Application\Response\UserConnectionCollectionResponse;
use LaSalle\StudentTeacher\User\Application\Response\UserConnectionResponse;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Aggregate\UserConnection;
use LaSalle\StudentTeacher\User\Domain\Repository\UserConnectionRepository;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;

final class SearchUserConnectionsByCriteria
{
    private UserConnectionRepository $userConnectionRepository;
    private UserRepository $userRepository;

    public function __construct(UserConnectionRepository $userConnectionRepository, UserRepository $userRepository)
    {
        $this->userConnectionRepository = $userConnectionRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(SearchUserConnectionsByCriteriaRequest $request)
    {
        $this->ensureRequestAuthorCanExecute($request->getRequestAuthorId(), $request->getUserId());

        $user = $this->identifyUserById($request->getUserId());

        $criteria = new Criteria(
            Filters::fromValues($this->createFiltersByUserId($user)),
            Order::fromValues($request->getOrderBy(), $request->getOrder()),
            Operator::fromValue($request->getOperator()),
            $request->getOffset(),
            $request->getLimit()
        );

        $connections = $this->userConnectionRepository->matching($criteria);
        $this->checkIfConnectionsExist($connections);

        $buildResponse = Role::STUDENT === $this->verifyRole($user) ?
            $this->buildStudentResponse(...$connections) :
            $this->buildTeacherResponse();

        return new UserConnectionCollectionResponse(...$buildResponse);
    }

    private function createFiltersByUserId(User $user): array
    {
        if (Role::STUDENT === $this->verifyRole($user)) {
            return [['field' => 'studentId', 'operator' => '=', 'value' => $user->getId()->toString()]];
        }
        return [['field' => 'teacherId', 'operator' => '=', 'value' => $user->getId()->toString()]];
    }

    private function identifyUserById(string $id): User
    {
        $user = $this->userRepository->ofId($this->createIdFromPrimitive($id));
        if (null === $user) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    private function createIdFromPrimitive(string $uuid): Uuid
    {
        try {
            return new Uuid($uuid);
        } catch (InvalidUuidException $error) {
            throw new InvalidArgumentValidationException($error->getMessage());
        }
    }

    private function verifyRole(User $user): string
    {
        if (in_array(Role::STUDENT, $user->getRoles()->toArrayOfPrimitives())) {
            return Role::STUDENT;
        }
        if (in_array(Role::TEACHER, $user->getRoles()->toArrayOfPrimitives())) {
            return Role::TEACHER;
        }
        throw new RoleIsNotStudentOrTeacherException();
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

    private function buildStudentResponse(UserConnection ...$connections): array
    {
        return array_map(
            static function (UserConnection $connection) {
                return new UserConnectionResponse(
                    $connection->getStudentId()->toString(),
                    $connection->getTeacherId()->toString(),
                    (string) $connection->getState(),
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