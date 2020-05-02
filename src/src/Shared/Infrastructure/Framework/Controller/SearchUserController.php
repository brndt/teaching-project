<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\User\Application\Service\SearchUsersByCriteria;
use Symfony\Component\HttpFoundation\Response;

final class SearchUserController extends AbstractFOSRestController
{
    private SearchUsersByCriteria $searchUser;

    public function __construct(SearchUsersByCriteria $searchUser)
    {
        $this->searchUser = $searchUser;
    }

    /**
     * @Rest\Get("/api/v1/users/{id}")
     */
    public function getAction(string $id): Response
    {
        $filters = [['field' => 'id', 'operator' => '=', 'value' => $id]];
        $criteria = new Criteria(Filters::fromValues($filters), Order::fromValues(null, null), null, null, null);

        $userResponse = ($this->searchUser)($criteria);

        return $this->handleView($this->view($userResponse, Response::HTTP_OK));
    }
}