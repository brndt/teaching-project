<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\CoursePermission\Infrastructure\Framework\Controller;

use DateTimeImmutable;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\CoursePermission\Application\Request\CreateStudentCoursePermissionRequest;
use LaSalle\StudentTeacher\Resource\CoursePermission\Application\Service\CreateStudentCoursePermissionService;
use Symfony\Component\HttpFoundation\Response;

final class CreateStudentCoursePermissionController extends AbstractFOSRestController
{
    public function __construct(private CreateStudentCoursePermissionService $createStudentCoursePermissionService)
    {
    }

    /**
     * @Rest\Post("/api/v1/panel/course_permission")
     * @RequestParam(name="courseId")
     * @RequestParam(name="studentId")
     * @RequestParam(name="status")
     * @RequestParam(name="until", nullable=true)
     */
    public function __invoke(ParamFetcher $paramFetcher): Response
    {
        $requestAuthorId = $this->getUser()->getId();

        $courseId = $paramFetcher->get('courseId');
        $studentId = $paramFetcher->get('studentId');
        $status = $paramFetcher->get('status');
        $until = $paramFetcher->get('until') ? new DateTimeImmutable($paramFetcher->get('until')) : null;

        ($this->createStudentCoursePermissionService)(
            new CreateStudentCoursePermissionRequest($requestAuthorId, $courseId, $studentId, $status, $until)
        );

        return $this->handleView(
            $this->view(['message' => 'Course permission has been successfully created'], Response::HTTP_CREATED)
        );
    }

}
