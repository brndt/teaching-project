<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use DateTimeImmutable;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Unit\Application\CreateUnitRequest;
use LaSalle\StudentTeacher\Resource\Unit\Application\CreateUnitService;
use Symfony\Component\HttpFoundation\Response;

final class CreateUnitController extends AbstractFOSRestController
{
    public function __construct(private CreateUnitService $createUnitService)
    {
    }

    /**
     * @Rest\Post("/api/v1/panel/courses/{courseId}/units")
     * @RequestParam(name="name")
     * @RequestParam(name="description", nullable=true)
     * @RequestParam(name="level")
     * @RequestParam(name="status")
     */
    public function postAction(string $courseId, ParamFetcher $paramFetcher): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $name = $paramFetcher->get('name');
        $description = $paramFetcher->get('description');
        $level = $paramFetcher->get('level');
        $status = $paramFetcher->get('status');

        ($this->createUnitService)(
            new CreateUnitRequest(
                $requestAuthorId,
                $courseId,
                $name,
                $description,
                $level,
                new DateTimeImmutable(),
                null,
                $status
            )
        );

        return $this->handleView(
            $this->view(['message' => 'Unit has been successfully created'], Response::HTTP_CREATED)
        );
    }
}
