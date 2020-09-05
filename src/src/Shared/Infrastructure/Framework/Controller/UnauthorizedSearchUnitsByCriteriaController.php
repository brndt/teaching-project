<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\UnauthorizedSearchUnitsByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\UnauthorizedSearchUnitsByCriteriaService;
use Symfony\Component\HttpFoundation\Response;

class UnauthorizedSearchUnitsByCriteriaController extends AbstractFOSRestController
{
    private UnauthorizedSearchUnitsByCriteriaService $unauthorizedSearchUnitByCriteriaService;

    public function __construct(UnauthorizedSearchUnitsByCriteriaService $unauthorizedSearchUnitByCriteriaService)
    {
        $this->unauthorizedSearchUnitByCriteriaService = $unauthorizedSearchUnitByCriteriaService;
    }

    /**
     * @Rest\Get("/api/v1/units")
     * @QueryParam(name="teacherId", strict=true, nullable=true),
     * @QueryParam(name="courseId", strict=true, nullable=true)
     * @QueryParam(name="orderBy", strict=true, nullable=true)
     * @QueryParam(name="order", strict=true, nullable=true, default="none")
     * @QueryParam(name="offset", strict=true, nullable=true, requirements="\d+")
     * @QueryParam(name="limit", strict=true, nullable=true, requirements="\d+", default=10)
     */
    public function postAction(ParamFetcher $paramFetcher): Response
    {
        $teacherId = $paramFetcher->get('teacherId');
        $courseId = $paramFetcher->get('courseId');

        $filters = [['field' => 'status', 'operator' => '=', 'value' => 'published']];

        if (true !== empty($courseId)) {
            $filters[] = ['field' => 'courseId', 'operator' => '=', 'value' => $courseId];
        }

        $orderBy = $paramFetcher->get('orderBy');
        $order = $paramFetcher->get('order');
        $operator = 'AND';
        $offset = (int)$paramFetcher->get('offset');
        $limit = (int)$paramFetcher->get('limit');

        $coursesResponse = ($this->unauthorizedSearchUnitByCriteriaService)(
            new UnauthorizedSearchUnitsByCriteriaRequest(
                $filters,
                $orderBy,
                $order,
                $operator,
                $offset,
                $limit
            )
        );

        return $this->handleView(
            $this->view($coursesResponse, Response::HTTP_CREATED)
        );
    }

}
