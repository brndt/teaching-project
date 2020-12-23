<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use DateTimeImmutable;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\CreateTestResourceStudentAnswerRequest;
use LaSalle\StudentTeacher\Resource\Application\Request\CreateVideoResourceStudentAnswerRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\CreateTestResourceStudentAnswerService;
use LaSalle\StudentTeacher\Resource\Application\Service\CreateVideoResourceStudentAnswerService;
use Symfony\Component\HttpFoundation\Response;

final class CreateVideoResourceStudentAnswerController extends AbstractFOSRestController
{
    public function __construct(private CreateVideoResourceStudentAnswerService $createVideoResourceStudentAnswerService)
    {
    }

    /**
     * @Rest\Post("/api/v1/panel/video_resource_student_permission")
     * @RequestParam(name="resourceId")
     * @RequestParam(name="studentAnswer")
     */
    public function __invoke(ParamFetcher $paramFetcher): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $resourceId = $paramFetcher->get('resourceId');
        $studentAnswer = $paramFetcher->get('studentAnswer');

        ($this->createVideoResourceStudentAnswerService)(
            new CreateVideoResourceStudentAnswerRequest(
                $requestAuthorId,
                $resourceId,
                $studentAnswer
            )
        );

        return $this->handleView(
            $this->view(
                ['message' => 'Video resource student answer has been successfully created'],
                Response::HTTP_CREATED
            )
        );
    }
}
