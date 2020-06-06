<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\Resource\Application\Request\SearchCourseRequest;
use LaSalle\StudentTeacher\Resource\Application\Request\SearchCoursesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\SearchCoursesByCriteriaService;
use LaSalle\StudentTeacher\Resource\Application\Service\SearchCourseService;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizatedSearchCourseController extends AbstractFOSRestController
{
    private SearchCourseService $searchCourse;

    public function __construct(SearchCourseService $searchCourse)
    {
        $this->searchCourse = $searchCourse;
    }

    /**
     * @Rest\Get("/api/v1/panel/courses/{courseId}")
     */
    public function getAction(string $courseId): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $userResponse = ($this->searchCourse)(new SearchCourseRequest($requestAuthorId, $courseId));
        return $this->handleView($this->view($userResponse, Response::HTTP_OK));
    }
}
