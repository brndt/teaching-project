<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\User\Application\Response\UserCredentialsCollectionResponse;
use LaSalle\StudentTeacher\User\Application\Response\UserCredentialsResponse;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;

final class SearchUserCredentialsByCriteria extends UserService
{
    public function __invoke(Criteria $criteria): UserCredentialsCollectionResponse
    {
        $users = $this->userRepository->matching($criteria);
        $this->ensureUsersExist($users);
        return new UserCredentialsCollectionResponse(...$this->buildResponse(...$users));
    }

    private function buildResponse(User ...$users): array
    {
        return array_map(
            static function (User $user) {
                return new UserCredentialsResponse(
                    $user->getId()->toString(),
                    $user->getEmail()->toString(),
                    $user->getPassword()->toString(),
                    $user->getRoles()->getArrayOfPrimitives(),
                    $user->getEnabled()
                );
            },
            $users
        );
    }
}