<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Unit\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Unit\Application\Response\UpdateUnitRequest;
use LaSalle\StudentTeacher\Resource\Unit\Application\Service\UpdateUnitService;
use Symfony\Component\HttpFoundation\Response;

final class UpdateUnitController extends AbstractFOSRestController
{
    public function __construct(private UpdateUnitService $updateUnitService)
    {
    }

    /**
     * @Rest\Patch("/api/v1/panel/courses/{courseId}/units/{unitId}")
     * @RequestParam(name="name")
     * @RequestParam(name="description", nullable=true)
     * @RequestParam(name="level")
     * @RequestParam(name="status")
     */
    public function __invoke(ParamFetcher $paramFetcher, string $courseId, string $unitId): Response
    {
        $requestAuthorId = $this->getUser()->getId();
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
