<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Resource\Application\Request\CreateVideoResourceRequest;
use LaSalle\StudentTeacher\Resource\Resource\Application\Service\CreateVideoResourceService;
use Symfony\Component\HttpFoundation\Response;

final class CreateVideoResourceController extends AbstractFOSRestController
{
    public function __construct(private CreateVideoResourceService $createVideoResourceService)
    {
    }

    /**
     * @Rest\Post("/api/v1/panel/courses/{courseId}/units/{unitId}/video_resources")
     * @RequestParam(name="name")
     * @RequestParam(name="description", nullable=true)
     * @RequestParam(name="content")
     * @RequestParam(name="status")
     * @RequestParam(name="videoURL")
     * @RequestParam(name="videoDescription")
     */
    public function __invoke(ParamFetcher $paramFetcher, string $courseId, string $unitId): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $name = $paramFetcher->get('name');
        $description = $paramFetcher->get('description');
        $content = $paramFetcher->get('content');
        $status = $paramFetcher->get('status');
        $videoURL = $paramFetcher->get('videoURL');
        $videoDescription = $paramFetcher->get('videoDescription');

        ($this->createVideoResourceService)(
            new CreateVideoResourceRequest(
                $requestAuthorId,
                $unitId,
                $name,
                $description,
                $content,
                $status,
                $videoURL,
                $videoDescription
            )
        );

        return $this->handleView(
            $this->view(['message' => 'Resource has been successfully created'], Response::HTTP_CREATED)
        );
    }
}
