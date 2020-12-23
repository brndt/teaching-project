<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchTestResourceStudentAnswerRequest;
use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchVideoResourceStudentAnswerRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\AuthorizedSearchTestResourceStudentAnswerService;
use LaSalle\StudentTeacher\Resource\Application\Service\AuthorizedSearchVideoResourceStudentAnswerService;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizedSearchVideoResourceStudentAnswerController extends AbstractFOSRestController
{
    public function __construct(private AuthorizedSearchVideoResourceStudentAnswerService $searchVideoResourceStudentAnswerService)
    {
    }

    /**
     * @Rest\Get("/api/v1/panel/video_resource_student_permission")
     * @QueryParam(name="resourceId", nullable=false)
     * @QueryParam(name="studentId", nullable=false)
     */
    public function __invoke(ParamFetcher $paramFetcher): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $resourceId = $paramFetcher->get('resourceId');
        $studentId = $paramFetcher->get('studentId');

        $studentAnswerResponse = ($this->searchVideoResourceStudentAnswerService)(
            new AuthorizedSearchVIdeoResourceStudentAnswerRequest(
                $requestAuthorId,
                $resourceId,
                $studentId
            )
        );

        return $this->handleView($this->view($studentAnswerResponse, Response::HTTP_OK));
    }
}
