<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\CoursePermission\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\CoursePermission\Application\Request\AuthorizedSearchStudentCoursePermissionRequest;
use LaSalle\StudentTeacher\Resource\CoursePermission\Application\Service\AuthorizedSearchStudentCoursePermissionService;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizedSearchStudentCoursePermissionController extends AbstractFOSRestController
{
    public function __construct(
        private AuthorizedSearchStudentCoursePermissionService $searchStudentCoursePermissionService
    ) {
    }

    /**
     * @Rest\Get("/api/v1/panel/course_permission/")
     * @QueryParam(name="courseId", nullable=false)
     * @QueryParam(name="studentId", nullable=false)
     */
    public function __invoke(ParamFetcher $paramFetcher): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $courseId = $paramFetcher->get('courseId');
        $studentId = $paramFetcher->get('studentId');

        $coursePermissionResponse = ($this->searchStudentCoursePermissionService)(
            new AuthorizedSearchStudentCoursePermissionRequest($requestAuthorId, $courseId, $studentId)
        );

        return $this->handleView(
            $this->view($coursePermissionResponse, Response::HTTP_OK)
        );
    }

}
