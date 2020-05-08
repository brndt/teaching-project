<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\User\Application\Request\SearchUserConnectionsByCriteriaRequest;
use LaSalle\StudentTeacher\User\Application\Service\SearchUserConnectionsByCriteria;
use Symfony\Component\HttpFoundation\Response;

final class SearchUserConnectionsController extends AbstractFOSRestController
{
    private SearchUserConnectionsByCriteria $searchConnections;

    public function __construct(SearchUserConnectionsByCriteria $searchConnections)
    {
        $this->searchConnections = $searchConnections;
    }

    /**
     * @Rest\Get("/api/v1/users/{userId}/connections")
     * @QueryParam(name="order_by", strict=true, nullable=true)
     * @QueryParam(name="order", strict=true, nullable=true, default="none")
     * @QueryParam(name="offset", strict=true, nullable=true, requirements="\d+")
     * @QueryParam(name="limit", strict=true, nullable=true, requirements="\d+", default=10)
     */
    public function getAction(ParamFetcher $paramFetcher, string $userId): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $orderBy = $paramFetcher->get('orderBy');
        $order = $paramFetcher->get('order');
        $operator = 'OR';
        $offset = (int) $paramFetcher->get('offset');
        $limit = (int) $paramFetcher->get('limit');

        $connections = ($this->searchConnections)(new SearchUserConnectionsByCriteriaRequest($requestAuthorId, $userId, $orderBy, $order, $operator, $offset, $limit));

        return $this->handleView(
            $this->view($connections, Response::HTTP_OK)
        );
    }
}