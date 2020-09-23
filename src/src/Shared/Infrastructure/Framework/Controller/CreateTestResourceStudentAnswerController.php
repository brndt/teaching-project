<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use DateTimeImmutable;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\CreateTestResourceStudentAnswerRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\CreateTestResourceStudentAnswerService;
use Symfony\Component\HttpFoundation\Response;

final class CreateTestResourceStudentAnswerController extends AbstractFOSRestController
{
    private CreateTestResourceStudentAnswerService $createTestResourceStudentAnswerService;

    public function __construct(CreateTestResourceStudentAnswerService $createTestResourceStudentAnswerService)
    {
        $this->createTestResourceStudentAnswerService = $createTestResourceStudentAnswerService;
    }

    /**
     * @Rest\Post("/api/v1/panel/test_resource_student_permission")
     * @RequestParam(name="resourceId")
     * @RequestParam(name="points")
     * @RequestParam(name="teacherComment")
     * @RequestParam(name="status")
     * @RequestParam(name="until", nullable=true)
     * @RequestParam(name="assumptions", map=true)
     */
    public function __invoke(ParamFetcher $paramFetcher): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $resourceId = $paramFetcher->get('resourceId');
        $status = $paramFetcher->get('status');
        $assumptions = $paramFetcher->get('assumptions');

        ($this->createTestResourceStudentAnswerService)(
            new CreateTestResourceStudentAnswerRequest(
                $requestAuthorId,
                $resourceId,
                $status,
                $assumptions
            )
        );

        return $this->handleView(
            $this->view(
                ['message' => 'Test resource student answer has been successfully created'],
                Response::HTTP_CREATED
            )
        );
    }
}