<?php
declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;


use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchResourcesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\AuthorizedSearchResourcesByCriteriaService;
use Symfony\Component\HttpFoundation\Response;

class AuthorizedSearchResourcesByCriteriaController extends AbstractFOSRestController
{
    private AuthorizedSearchResourcesByCriteriaService $searchResources;

    public function __construct(AuthorizedSearchResourcesByCriteriaService $searchResources)
    {
        $this->searchResources = $searchResources;
    }

    /**
     * @Rest\Get("/api/v1/panel/courses/{courseId}/units/{unitId}/resources")
     * @QueryParam(name="orderBy", strict=true, nullable=true)
     * @QueryParam(name="order", strict=true, nullable=true, default="none")
     * @QueryParam(name="offset", strict=true, nullable=true, requirements="\d+")
     * @QueryParam(name="limit", strict=true, nullable=true, requirements="\d+", default=10)
     */
    public function getAction(string $courseId, string $unitId, ParamFetcher $paramFetcher): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $orderBy = $paramFetcher->get('orderBy');
        $order = $paramFetcher->get('order');
        $operator = 'AND';
        $offset = (int)$paramFetcher->get('offset');
        $limit = (int)$paramFetcher->get('limit');

        $resources = ($this->searchResources)(
            new AuthorizedSearchResourcesByCriteriaRequest(
                $requestAuthorId,
                $courseId,
                $unitId,
                $orderBy,
                $order,
                $operator,
                $offset,
                $limit
            )
        );

        return $this->handleView(
            $this->view($resources, Response::HTTP_OK)
        );
    }
}
