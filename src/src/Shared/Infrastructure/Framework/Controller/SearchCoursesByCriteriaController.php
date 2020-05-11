<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\SearchCoursesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\SearchCoursesByCriteriaService;
use Symfony\Component\HttpFoundation\Response;

final class SearchCoursesByCriteriaController extends AbstractFOSRestController
{
    private SearchCoursesByCriteriaService $searchCourses;

    public function __construct(SearchCoursesByCriteriaService $searchCourses)
    {
        $this->searchCourses = $searchCourses;
    }

    /**
     * @Rest\Get("/api/v1/courses")
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
        $offset = (int)$paramFetcher->get('offset');
        $limit = (int)$paramFetcher->get('limit');

        $courses = ($this->searchCourses)(
            new SearchCoursesByCriteriaRequest($filters, $orderBy, $order, $operator, $offset, $limit)
        );

        return $this->handleView(
            $this->view($courses, Response::HTTP_OK)
        );
    }
}