<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\UpdateUnitRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\UpdateUnitService;
use Symfony\Component\HttpFoundation\Response;

final class UpdateUnitController extends AbstractFOSRestController
{
    private UpdateUnitService $updateUnitService;

    public function __construct(UpdateUnitService $updateUnitService)
    {
        $this->updateUnitService = $updateUnitService;
    }

    /**
     * @Rest\Patch("/api/v1/panel/units/{unitId}")
     * @RequestParam(name="courseId")
     * @RequestParam(name="name")
     * @RequestParam(name="description", nullable=true)
     * @RequestParam(name="level")
     * @RequestParam(name="status")
     */
    public function postAction(ParamFetcher $paramFetcher, string $unitId): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $courseId = $paramFetcher->get('courseId');
        $name = $paramFetcher->get('name');
        $description = $paramFetcher->get('description');
        $level = $paramFetcher->get('level');
        $status = $paramFetcher->get('status');

        ($this->updateUnitService)(
            new UpdateUnitRequest($requestAuthorId, $courseId, $unitId, $name, $description, $level, $status)
        );

        return $this->handleView(
            $this->view(['message' => 'Unit has been successfully updated'], Response::HTTP_CREATED)
        );
    }
}
