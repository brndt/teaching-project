<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Response\UserCredentialsCollectionResponse;
use LaSalle\StudentTeacher\User\Application\Response\UserCredentialsResponse;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;

final class SearchUserCredentialsByCriteria
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(Criteria $criteria): UserCredentialsCollectionResponse
    {
        $users = $this->userRepository->matching($criteria);

        if (true === empty($users)) {
            throw new UserNotFoundException();
        }

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
                    $user->getRoles()->toArrayOfPrimitives(),
                    $user->getEnabled()
                );
            },
            $users
        );
    }
}