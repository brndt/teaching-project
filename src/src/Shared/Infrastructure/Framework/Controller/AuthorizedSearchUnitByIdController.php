<?php
declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;


use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchUnitByIdRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\AuthorizedSearchUnitByIdService;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizedSearchUnitByIdController extends AbstractFOSRestController
{
    private AuthorizedSearchUnitByIdService $searchUnit;

    public function __construct(AuthorizedSearchUnitByIdService $searchUnit)
    {
        $this->searchUnit = $searchUnit;
    }

    /**
     * @Rest\Get("/api/v1/panel/courses/{courseId}/units/{unitId}")
     */
    public function getAction(string $courseId, string $unitId): Response
    {
        $requestAuthorId = $this->getUser()->getId();

        $unit = ($this->searchUnit)(
            new AuthorizedSearchUnitByIdRequest($requestAuthorId, $courseId, $unitId)
        );

        return $this->handleView(
            $this->view($unit, Response::HTTP_OK)
        );
    }

}
