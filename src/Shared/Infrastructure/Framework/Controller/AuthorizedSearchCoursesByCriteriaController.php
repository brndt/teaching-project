<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Exception\CourseNotFoundException;
use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchCoursesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\AuthorizedSearchCoursesByCriteriaService;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

final class AuthorizedSearchCoursesByCriteriaController extends AbstractFOSRestController
{
    public function __construct(private AuthorizedSearchCoursesByCriteriaService $searchCourses)
    {
    }

    /**
     * @Rest\Get("/api/v1/panel/courses")
     * @QueryParam(name="teacherId", strict=true, nullable=true)
     * @QueryParam(name="orderBy", strict=true, nullable=true)
     * @QueryParam(name="order", strict=true, nullable=true, default="none")
     * @QueryParam(name="offset", strict=true, nullable=true, requirements="\d+")
     * @QueryParam(name="limit", strict=true, nullable=true, requirements="\d+", default=10)
     */
    public function getAction(ParamFetcher $paramFetcher): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $teacherId = $paramFetcher->get('teacherId');
        $orderBy = $paramFetcher->get('orderBy');
        $order = $paramFetcher->get('order');
        $operator = 'AND';
        $offset = (int)$paramFetcher->get('offset');
        $limit = (int)$paramFetcher->get('limit');

        $courses = ($this->searchCourses)(
            new AuthorizedSearchCoursesByCriteriaRequest(
                $requestAuthorId,
                $teacherId,
                $orderBy,
                $order,
                $operator,
                $offset,
                $limit
            )
        );

        return $this->handleView(
            $this->view($courses, Response::HTTP_OK)
        );
    }
}
