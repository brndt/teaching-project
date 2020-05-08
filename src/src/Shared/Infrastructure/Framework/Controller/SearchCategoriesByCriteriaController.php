<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\Resource\Application\Request\SearchCategoriesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\SearchCategoriesByCriteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\User\Application\Request\SearchUserConnectionsByCriteriaRequest;
use LaSalle\StudentTeacher\User\Application\Service\SearchUserConnectionsByCriteria;
use Symfony\Component\HttpFoundation\Response;

final class SearchCategoriesByCriteriaController extends AbstractFOSRestController
{
    private SearchCategoriesByCriteria $searchCategoriesByCriteria;

    public function __construct(SearchCategoriesByCriteria $searchCategoriesByCriteria)
    {
        $this->searchCategoriesByCriteria = $searchCategoriesByCriteria;
    }

    /**
     * @Rest\Get("/api/v1/categories/")
     * @QueryParam(name="name", strict=true, nullable=true)
     * @QueryParam(name="orderBy", strict=true, nullable=true)
     * @QueryParam(name="order", strict=true, nullable=true, default="none")
     * @QueryParam(name="offset", strict=true, nullable=true, requirements="\d+")
     * @QueryParam(name="limit", strict=true, nullable=true, requirements="\d+", default=10)
     */
    public function getAction(ParamFetcher $paramFetcher): Response
    {
        $name = $paramFetcher->get('name');
        $filters = empty($name) ? [] : [['field' => 'name', 'operator' => 'CONTAINS', 'value' => $name]];
        $orderBy = $paramFetcher->get('orderBy');
        $order = $paramFetcher->get('order');
        $operator = 'AND';
        $offset = (int) $paramFetcher->get('offset');
        $limit = (int) $paramFetcher->get('limit');

        $categories = ($this->searchCategoriesByCriteria)(new SearchCategoriesByCriteriaRequest($filters, $orderBy, $order, $operator, $offset, $limit));

        return $this->handleView(
            $this->view($categories, Response::HTTP_OK)
        );
    }
}