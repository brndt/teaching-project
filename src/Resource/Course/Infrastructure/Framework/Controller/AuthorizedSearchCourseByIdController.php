<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Course\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\Resource\Course\Application\Request\AuthorizedSearchCourseByIdRequest;
use LaSalle\StudentTeacher\Resource\Course\Application\Service\AuthorizedSearchCourseByIdService;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizedSearchCourseByIdController extends AbstractFOSRestController
{
    public function __construct(private AuthorizedSearchCourseByIdService $searchCourse)
    {
    }

    /**
     * @Rest\Get("/api/v1/panel/courses/{courseId}")
     */
    public function __invoke(string $courseId): Response
    {
        $requestAuthorId = $this->getUser()->getId();

        $courses = ($this->searchCourse)(
            new AuthorizedSearchCourseByIdRequest($requestAuthorId, $courseId)
        );

        return $this->handleView(
            $this->view($courses, Response::HTTP_OK)
        );
    }
}
