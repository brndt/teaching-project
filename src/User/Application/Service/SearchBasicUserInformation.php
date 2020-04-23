<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\SearchBasicUserInformationRequest;
use LaSalle\StudentTeacher\User\Application\Response\BasicUserInformationResponse;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;

final class SearchBasicUserInformation
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Filters $filters, Order $order, ?int $limit, ?int $offset): array
    {
        $criteria = new Criteria($filters, $order, $offset, $limit);

        $users = $this->repository->matching($criteria);

        if (null === $users) {
            throw new UserNotFoundException();
        }

        var_dump($users);

        return $users;

        /*return new BasicUserInformationResponse(
            $users->getId()->toString(),
            $user->getEmail()->toString(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getRoles()->toArrayOfPrimitives(),
            $user->getImage(),
            $user->getEducation(),
            $user->getExperience(),
        );*/
    }
}