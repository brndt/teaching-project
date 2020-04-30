<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\User\Application\Service\SearchUsersByCriteria;
use Symfony\Component\HttpFoundation\Response;

final class SearchUsersController extends AbstractFOSRestController
{
    private SearchUsersByCriteria $searchUser;

    public function __construct(SearchUsersByCriteria $searchUser)
    {
        $this->searchUser = $searchUser;
    }

    /**
     * @Rest\Get("/api/v1/users")
     * @QueryParam(name="role", strict=true, nullable=true, requirements="student|teacher")
     * @QueryParam(name="orderBy", strict=true, nullable=true)
     * @QueryParam(name="order", strict=true, nullable=true, default="none")
     * @QueryParam(name="offset", strict=true, nullable=true, requirements="\d+")
     * @QueryParam(name="limit", strict=true, nullable=true, requirements="\d+", default=10)
     */
    public function getAction(ParamFetcher $paramFetcher): Response
    {
        $roles = $paramFetcher->get('role');

        $filters = [];
        if (false === empty($roles)) {
            $filters = [['field' => 'roles', 'operator' => 'CONTAINS', 'value' => $roles]];
        }

        $orderBy = $paramFetcher->get('orderBy');
        $order = $paramFetcher->get('order');
        $offset = (int)$paramFetcher->get('offset');
        $limit = (int)$paramFetcher->get('limit');

        $criteria = new Criteria(Filters::fromValues($filters), Order::fromValues($orderBy, $order), $offset, $limit);

        $userResponse = ($this->searchUser)($criteria);

        return $this->handleView($this->view($userResponse, Response::HTTP_OK));
    }
}