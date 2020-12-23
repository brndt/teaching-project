<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchVideoResourceByIdRequest;
use LaSalle\StudentTeacher\Resource\Application\Request\CreateVideoResourceRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\AuthorizedSearchVideoResourceByIdService;
use LaSalle\StudentTeacher\Resource\Application\Service\CreateVideoResourceService;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizedSearchVideoResourceByIdController extends AbstractFOSRestController
{
    public function __construct(private AuthorizedSearchVideoResourceByIdService $searchVideoResourceByIdService)
    {
    }

    /**
     * @Rest\Get("/api/v1/panel/courses/{courseId}/units/{unitId}/video_resources/{resourceId}")
     */
    public function __invoke(ParamFetcher $paramFetcher, string $courseId, string $unitId, string $resourceId): Response
    {
        $requestAuthorId = $this->getUser()->getId();

        $resourceResponse = ($this->searchVideoResourceByIdService)(
            new AuthorizedSearchVideoResourceByIdRequest(
                $requestAuthorId,
                $resourceId
            )
        );

        return $this->handleView(
            $this->view($resourceResponse, Response::HTTP_OK)
        );
    }
}
