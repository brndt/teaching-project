<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\Resource\Application\Request\SearchCoursesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\SearchCoursesByCriteriaService;
use Symfony\Component\HttpFoundation\Response;

final class SearchCourseController extends AbstractFOSRestController
{
    private SearchCoursesByCriteriaService $searchCourse;

    public function __construct(SearchCoursesByCriteriaService $searchCourse)
    {
        $this->searchCourse = $searchCourse;
    }

    /**
     * @Rest\Get("/api/v1/courses/{courseId}")
     */
    public function getAction(string $courseId): Response
    {
        $filters = [['field' => 'id', 'operator' => '=', 'value' => $courseId]];

        $userResponse = ($this->searchCourse)(
            new SearchCoursesByCriteriaRequest($filters, null, null, null, null, null)
        );

        return $this->handleView($this->view($userResponse, Response::HTTP_OK));
    }
}