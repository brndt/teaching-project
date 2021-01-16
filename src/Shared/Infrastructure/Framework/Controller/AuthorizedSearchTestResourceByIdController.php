<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Resource\Application\AuthorizedSearchTestResourceByIdRequest;
use LaSalle\StudentTeacher\Resource\Resource\Application\AuthorizedSearchTestResourceByIdService;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizedSearchTestResourceByIdController extends AbstractFOSRestController
{
    public function __construct(private AuthorizedSearchTestResourceByIdService $searchTestResourceByIdService)
    {
    }

    /**
     * @Rest\Get("/api/v1/panel/courses/{courseId}/units/{unitId}/test_resources/{resourceId}")
     */
    public function __invoke(ParamFetcher $paramFetcher, string $courseId, string $unitId, string $resourceId): Response
    {
        $requestAuthorId = $this->getUser()->getId();

        $resourceResponse = ($this->searchTestResourceByIdService)(
            new AuthorizedSearchTestResourceByIdRequest(
                $requestAuthorId,
                $resourceId
            )
        );

        return $this->handleView(
            $this->view($resourceResponse, Response::HTTP_OK)
        );
    }
}
