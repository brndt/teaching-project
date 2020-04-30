<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Response\UserCollectionResponse;
use LaSalle\StudentTeacher\User\Application\Response\UserResponse;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;

final class SearchUsersByCriteria
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(Criteria $criteria): UserCollectionResponse
    {
        $users = $this->userRepository->matching($criteria);
        $this->checkIfExist($users);
        return new UserCollectionResponse(...$this->buildResponse(...$users));
    }

    private function checkIfExist(array $users): void
    {
        if (true === empty($users)) {
            throw new UserNotFoundException();
        }
    }

    private function buildResponse(User ...$users): array
    {
        return array_map(
            static function (User $user) {
                return new UserResponse(
                    $user->getId()->toString(),
                    $user->getFirstName(),
                    $user->getLastName(),
                    $user->getRoles()->toArrayOfPrimitives(),
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