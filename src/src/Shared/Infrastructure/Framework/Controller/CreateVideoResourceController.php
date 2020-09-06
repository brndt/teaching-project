<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\CreateVideoResourceRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\CreateVideoResourceService;
use Symfony\Component\HttpFoundation\Response;

final class CreateVideoResourceController extends AbstractFOSRestController
{
    private CreateVideoResourceService $createVideoResourceService;

    public function __construct(CreateVideoResourceService $createUnitService)
    {
        $this->createVideoResourceService = $createUnitService;
    }

    /**
     * @Rest\Post("/api/v1/panel/resources")
     * @RequestParam(name="unitId")
     * @RequestParam(name="name")
     * @RequestParam(name="description", nullable=true)
     * @RequestParam(name="content")
     * @RequestParam(name="status")
     * @RequestParam(name="videoURL")
     * @RequestParam(name="text")
     */
    public function __invoke(ParamFetcher $paramFetcher): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $unitId = $paramFetcher->get('unitId');
        $name = $paramFetcher->get('name');
        $description = $paramFetcher->get('description');
        $content = $paramFetcher->get('content');
        $status = $paramFetcher->get('status');
        $videoURL = $paramFetcher->get('videoURL');
        $text = $paramFetcher->get('text');

        ($this->createVideoResourceService)(
            new CreateVideoResourceRequest(
                $requestAuthorId,
                $unitId,
                $name,
                $description,
                $content,
                new \DateTimeImmutable(),
                null,
                $status,
                $videoURL,
                $text
            )
        );

        return $this->handleView(
            $this->view(['message' => 'Resource has been successfully created'], Response::HTTP_CREATED)
        );
    }
}
