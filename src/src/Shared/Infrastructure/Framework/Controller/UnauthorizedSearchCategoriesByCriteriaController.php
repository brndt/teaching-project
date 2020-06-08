<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\UnauthorizedSearchCategoriesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\UnauthorizedSearchCategoryByCriteriaService;
use Symfony\Component\HttpFoundation\Response;

final class UnauthorizedSearchCategoriesByCriteriaController extends AbstractFOSRestController
{
    private UnauthorizedSearchCategoryByCriteriaService $searchCategoriesByCriteria;

    public function __construct(UnauthorizedSearchCategoryByCriteriaService $searchCategoriesByCriteria)
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
        $filters = empty($name) ? [['field' => 'status', 'operator' => '=', 'value' => 'published']] : [
            ['field' => 'name', 'operator' => 'CONTAINS', 'value' => $name],
            ['field' => 'status', 'operator' => '=', 'value' => 'published']
        ];
        $orderBy = $paramFetcher->get('orderBy');
        $order = $paramFetcher->get('order');
        $operator = 'AND';
        $offset = (int)$paramFetcher->get('offset');
        $limit = (int)$paramFetcher->get('limit');

        $categories = ($this->searchCategoriesByCriteria)(
            new UnauthorizedSearchCategoriesByCriteriaRequest(
                $filters,
                $orderBy,
                $order,
                $operator,
                $offset,
                $limit
            )
        );

        return $this->handleView(
            $this->view($categories, Response::HTTP_OK)
        );
    }
}
