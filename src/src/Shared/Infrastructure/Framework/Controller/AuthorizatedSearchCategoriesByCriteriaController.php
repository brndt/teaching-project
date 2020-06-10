<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Exception\CategoryNotFound;
use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchCategoriesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\AuthorizedSearchCategoriesByCriteria;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizatedSearchCategoriesByCriteriaController extends AbstractFOSRestController
{
    private AuthorizedSearchCategoriesByCriteria $searchCategoriesByCriteria;

    public function __construct(AuthorizedSearchCategoriesByCriteria $searchCategoriesByCriteria)
    {
        $this->searchCategoriesByCriteria = $searchCategoriesByCriteria;
    }

    /**
     * @Rest\Get("/api/v1/panel/categories/")
     * @QueryParam(name="name", strict=true, nullable=true)
     * @QueryParam(name="orderBy", strict=true, nullable=true)
     * @QueryParam(name="order", strict=true, nullable=true, default="none")
     * @QueryParam(name="offset", strict=true, nullable=true, requirements="\d+")
     * @QueryParam(name="limit", strict=true, nullable=true, requirements="\d+", default=10)
     */
    public function getAction(ParamFetcher $paramFetcher): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $name = $paramFetcher->get('name');
        $filters = empty($name) ? [] : [['field' => 'name', 'operator' => 'CONTAINS', 'value' => $name]];
        $orderBy = $paramFetcher->get('orderBy');
        $order = $paramFetcher->get('order');
        $operator = 'AND';
        $offset = (int)$paramFetcher->get('offset');
        $limit = (int)$paramFetcher->get('limit');

        try {
            $categories = ($this->searchCategoriesByCriteria)(
                new AuthorizedSearchCategoriesByCriteriaRequest(
                    $requestAuthorId,
                    $filters,
                    $orderBy,
                    $order,
                    $operator,
                    $offset,
                    $limit
                )
            );
        } catch (CategoryNotFound $exception) {
            return $this->handleView(
                $this->view(null,Response::HTTP_NO_CONTENT)
            );
        }

        return $this->handleView(
            $this->view($categories, Response::HTTP_OK)
        );
    }
}
