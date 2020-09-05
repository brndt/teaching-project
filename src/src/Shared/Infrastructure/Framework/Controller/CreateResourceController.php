<?php
declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;


use DateTimeImmutable;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\CreateResourceRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\CreateResourceService;
use Symfony\Component\HttpFoundation\Response;

final class CreateResourceController extends AbstractFOSRestController
{
    private CreateResourceService $createResource;

    public function __construct(CreateResourceService $createResource)
    {
        $this->createResource = $createResource;
    }

    /**
     * @Rest\Post("/api/v1/panel/resourses")
     * @RequestParam(name="unitId")
     * @RequestParam(name="name")
     * @RequestParam(name="description", nullable=true)
     * @RequestParam(name="content")
     * @RequestParam(name="resourceType")
     * @RequestParam(name="status")
     * @param ParamFetcher $paramFetcher
     * @return Response
     */

    public function postAction(ParamFetcher $paramFetcher): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $unitId = $paramFetcher->get('unitId');
        $name = $paramFetcher->get('name');
        $description = $paramFetcher->get('description');
        $content = $paramFetcher->get('content');
        $resourceType = $paramFetcher->get('resourceType');
        $status = $paramFetcher->get('status');

        ($this->createResource)(new CreateResourceRequest($requestAuthorId, $unitId, $name, $description, $content, $resourceType, new DateTimeImmutable(), new DateTimeImmutable(), $status));

        return $this->handleView(
            $this->view(['message' => 'Resource has been successfully created'], Response::HTTP_CREATED)
        );
    }

}