<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use DateTimeImmutable;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\CreateTestRecourseStudentAnswerRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\CreateTestRecourseStudentAnswerService;
use Symfony\Component\HttpFoundation\Response;

final class CreateTestRecourseStudentAnswerController extends AbstractFOSRestController
{
    private CreateTestRecourseStudentAnswerService $createTestRecourseStudentAnswerService;

    public function __construct(CreateTestRecourseStudentAnswerService $createTestRecourseStudentAnswerService)
    {
        $this->createTestRecourseStudentAnswerService = $createTestRecourseStudentAnswerService;
    }

    /**
     * @Rest\Post("/api/v1/panel/test_recourse_student_permission")
     * @RequestParam(name="recourseId")
     * @RequestParam(name="points")
     * @RequestParam(name="teacherComment")
     * @RequestParam(name="status")
     * @RequestParam(name="until", nullable=true)
     * @RequestParam(name="assumptions", map=true)
     */
    public function __invoke(ParamFetcher $paramFetcher): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $recourseId = $paramFetcher->get('recourseId');
        $status = $paramFetcher->get('status');
        $assumptions = $paramFetcher->get('assumptions');

        ($this->createTestRecourseStudentAnswerService)(
            new CreateTestRecourseStudentAnswerRequest(
                $requestAuthorId,
                $recourseId,
                $status,
                $assumptions
            )
        );

        return $this->handleView(
            $this->view(
                ['message' => 'Test recourse student answer has been successfully created'],
                Response::HTTP_CREATED
            )
        );
    }
}
