<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\CreateTestResourceRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\CreateTestResourceService;
use Symfony\Component\HttpFoundation\Response;

final class CreateTestResourceController extends AbstractFOSRestController
{
    public function __construct(private CreateTestResourceService $createTestResourceService)
    {
    }

    /**
     * @Rest\Post("/api/v1/panel/courses/{courseId}/units/{unitId}/test_resources")
     * @RequestParam(name="name")
     * @RequestParam(name="description", nullable=true)
     * @RequestParam(name="content")
     * @RequestParam(name="status")
     * @RequestParam(name="questions", map=true)
     */
    public function __invoke(ParamFetcher $paramFetcher, string $courseId, string $unitId): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $name = $paramFetcher->get('name');
        $description = $paramFetcher->get('description');
        $content = $paramFetcher->get('content');
        $status = $paramFetcher->get('status');
        $questions = $paramFetcher->get('questions');

        ($this->createTestResourceService)(
            new CreateTestResourceRequest(
                $requestAuthorId,
                $unitId,
                $name,
                $description,
                $content,
                $status,
                $questions,
            )
        );

        return $this->handleView(
            $this->view(['message' => 'Test resource has been successfully created'], Response::HTTP_CREATED)
        );
    }
}
