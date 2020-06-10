<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Exception\CourseNotFoundException;
use LaSalle\StudentTeacher\Resource\Application\Request\UnauthorizedSearchCoursesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\UnauthorizedSearchCoursesByCriteriaService;
use Symfony\Component\HttpFoundation\Response;

final class UnauthorizedSearchCoursesByCriteriaController extends AbstractFOSRestController
{
    private UnauthorizedSearchCoursesByCriteriaService $searchCourses;

    public function __construct(UnauthorizedSearchCoursesByCriteriaService $searchCourses)
    {
        $this->searchCourses = $searchCourses;
    }

    /**
     * @Rest\Get("/api/v1/courses")
     * @QueryParam(name="teacherId", strict=true, nullable=true),
     * @QueryParam(name="categoryId", strict=true, nullable=true)
     * @QueryParam(name="orderBy", strict=true, nullable=true)
     * @QueryParam(name="order", strict=true, nullable=true, default="none")
     * @QueryParam(name="offset", strict=true, nullable=true, requirements="\d+")
     * @QueryParam(name="limit", strict=true, nullable=true, requirements="\d+", default=10)
     */
    public function getAction(ParamFetcher $paramFetcher): Response
    {
        $teacherId = $paramFetcher->get('teacherId');
        $categoryId = $paramFetcher->get('categoryId');

        $filters = [['field' => 'status', 'operator' => '=', 'value' => 'published']];

        if (true !== empty($teacherId)) {
            $filters[] = ['field' => 'teacherId', 'operator' => '=', 'value' => $teacherId];
        }

        if (true !== empty($categoryId)) {
            $filters[] = ['field' => 'categoryId', 'operator' => '=', 'value' => $categoryId];
        }

        $orderBy = $paramFetcher->get('orderBy');
        $order = $paramFetcher->get('order');
        $operator = 'AND';
        $offset = (int)$paramFetcher->get('offset');
        $limit = (int)$paramFetcher->get('limit');

        try {
            $courses = ($this->searchCourses)(
                new UnauthorizedSearchCoursesByCriteriaRequest(
                    $filters,
                    $orderBy,
                    $order,
                    $operator,
                    $offset,
                    $limit
                )
            );
        } catch (CourseNotFoundException $exception) {
            return $this->handleView(
                $this->view([], Response::HTTP_NO_CONTENT)
            );
        }

        return $this->handleView(
            $this->view($courses, Response::HTTP_OK)
        );
    }
}
