<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchUnitsByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\AuthorizedSearchUnitsByCriteriaService;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizedSearchUnitsByCriteriaController extends AbstractFOSRestController
{
    private AuthorizedSearchUnitsByCriteriaService $searchUnits;

    public function __construct(AuthorizedSearchUnitsByCriteriaService $searchUnits)
    {
        $this->searchUnits = $searchUnits;
    }

    /**
     * @Rest\Get("/api/v1/panel/courses/{courseId}/units")
     * @QueryParam(name="orderBy", strict=true, nullable=true)
     * @QueryParam(name="order", strict=true, nullable=true, default="none")
     * @QueryParam(name="offset", strict=true, nullable=true, requirements="\d+")
     * @QueryParam(name="limit", strict=true, nullable=true, requirements="\d+", default=10)
     */
    public function getAction(string $courseId, ParamFetcher $paramFetcher): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $orderBy = $paramFetcher->get('orderBy');
        $order = $paramFetcher->get('order');
        $operator = 'AND';
        $offset = (int)$paramFetcher->get('offset');
        $limit = (int)$paramFetcher->get('limit');

        $courses = ($this->searchUnits)(
            new AuthorizedSearchUnitsByCriteriaRequest(
                $requestAuthorId,
                $courseId,
                $orderBy,
                $order,
                $operator,
                $offset,
                $limit
            )
        );

        return $this->handleView(
            $this->view($courses, Response::HTTP_OK)
        );
    }
}
