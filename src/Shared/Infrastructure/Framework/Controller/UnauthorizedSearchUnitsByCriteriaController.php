<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Unit\Application\Request\UnauthorizedSearchUnitsByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Unit\Application\Service\UnauthorizedSearchUnitsByCriteriaService;
use Symfony\Component\HttpFoundation\Response;

class UnauthorizedSearchUnitsByCriteriaController extends AbstractFOSRestController
{
    public function __construct(
        private UnauthorizedSearchUnitsByCriteriaService $unauthorizedSearchUnitByCriteriaService
    ) {
    }

    /**
     * @Rest\Get("/api/v1/courses/{courseId}/units")
     * @QueryParam(name="teacherId", strict=true, nullable=true),
     * @QueryParam(name="orderBy", strict=true, nullable=true)
     * @QueryParam(name="order", strict=true, nullable=true, default="none")
     * @QueryParam(name="offset", strict=true, nullable=true, requirements="\d+")
     * @QueryParam(name="limit", strict=true, nullable=true, requirements="\d+", default=10)
     */
    public function postAction(ParamFetcher $paramFetcher, string $courseId): Response
    {
        $teacherId = $paramFetcher->get('teacherId');

        $filters = [['field' => 'status', 'operator' => '=', 'value' => 'published']];

        if (true !== empty($courseId)) {
            $filters[] = ['field' => 'courseId', 'operator' => '=', 'value' => $courseId];
        }

        $orderBy = $paramFetcher->get('orderBy');
        $order = $paramFetcher->get('order');
        $operator = 'AND';
        $offset = (int)$paramFetcher->get('offset');
        $limit = (int)$paramFetcher->get('limit');

        $unitsResponse = ($this->unauthorizedSearchUnitByCriteriaService)(
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
            $this->view($unitsResponse, Response::HTTP_OK)
        );
    }

}
