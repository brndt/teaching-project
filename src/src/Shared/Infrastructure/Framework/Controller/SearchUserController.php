<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\User\Application\Request\SearchUsersByCriteriaRequest;
use LaSalle\StudentTeacher\User\Application\Service\SearchUsersByCriteria;
use Symfony\Component\HttpFoundation\Response;

final class SearchUserController extends AbstractFOSRestController
{
    private SearchUsersByCriteria $searchUsersByCriteria;

    public function __construct(SearchUsersByCriteria $searchUsersByCriteria)
    {
        $this->searchUsersByCriteria = $searchUsersByCriteria;
    }

    /**
     * @Rest\Get("/api/v1/users/{userId}")
     */
    public function getAction(string $userId): Response
    {
        $filters = [['field' => 'id', 'operator' => '=', 'value' => $userId]];

        $userResponse = ($this->searchUsersByCriteria)(new SearchUsersByCriteriaRequest($filters, null, null, null, null, null));

        return $this->handleView($this->view($userResponse, Response::HTTP_OK));
    }
}