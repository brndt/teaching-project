<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\User\Application\Request\SearchUsersByCriteriaRequest;
use LaSalle\StudentTeacher\User\Application\Service\SearchUsersByCriteria;
use Symfony\Component\HttpFoundation\Response;

final class SearchUsersController extends AbstractFOSRestController
{
    private SearchUsersByCriteria $searchUsersByCriteria;

    public function __construct(SearchUsersByCriteria $searchUsersByCriteria)
    {
        $this->searchUsersByCriteria = $searchUsersByCriteria;
    }

    /**
     * @Rest\Get("/api/v1/users")
     * @QueryParam(name="role", nullable=true)
     * @QueryParam(name="orderBy", nullable=true)
     * @QueryParam(name="order", nullable=true, default="none")
     * @QueryParam(name="offset", nullable=true, requirements="\d+")
     * @QueryParam(name="limit", nullable=true, requirements="\d+", default=10)
     */
    public function getAction(ParamFetcher $paramFetcher): Response
    {
        $roles = $paramFetcher->get('role');

        $filters = empty($roles) ? [] : [['field' => 'roles', 'operator' => 'CONTAINS', 'value' => $roles]];
        $orderBy = $paramFetcher->get('orderBy');
        $order = $paramFetcher->get('order');
        $operator = null;
        $offset = (int) $paramFetcher->get('offset');
        $limit = (int) $paramFetcher->get('limit');

        $usersResponse = ($this->searchUsersByCriteria)(new SearchUsersByCriteriaRequest($filters, $orderBy, $order, $operator, $offset, $limit));

        return $this->handleView($this->view($usersResponse, Response::HTTP_OK));
    }
}