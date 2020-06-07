<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchCourseByIdRequest;
use LaSalle\StudentTeacher\Resource\Application\Request\SearchCoursesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\AuthorizedSearchCourseByIdService;
use LaSalle\StudentTeacher\Resource\Application\Service\AuthorizedSearchCoursesByCriteriaService;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

final class AuthorizatedSearchCourseByIdController extends AbstractFOSRestController
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

        $courses = ($this->searchCourse)(
            new AuthorizedSearchCourseByIdRequest($requestAuthorId, $courseId)
        );

        return $this->handleView(
            $this->view($courses, Response::HTTP_OK)
        );
    }
}
