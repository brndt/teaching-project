<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\Resource\Application\Exception\CourseNotFoundException;
use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchCourseByIdRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\AuthorizedSearchCourseByIdService;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizedSearchCourseByIdController extends AbstractFOSRestController
{
    private AuthorizedSearchCourseByIdService $searchCourse;

    public function __construct(AuthorizedSearchCourseByIdService $searchCourse)
    {
        $this->searchCourse = $searchCourse;
    }

    /**
     * @Rest\Get("/api/v1/panel/courses/{courseId}")
     */
    public function getAction(string $courseId): Response
    {
        $requestAuthorId = $this->getUser()->getId();

        try {
            $courses = ($this->searchCourse)(
                new AuthorizedSearchCourseByIdRequest($requestAuthorId, $courseId)
            );
        } catch (CourseNotFoundException $exception) {
            return $this->handleView(
                $this->view(null,Response::HTTP_NO_CONTENT)
            );
        }

        return $this->handleView(
            $this->view($courses, Response::HTTP_OK)
        );
    }
}
