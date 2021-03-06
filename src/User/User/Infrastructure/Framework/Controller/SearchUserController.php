<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\User\Shared\Application\Request\SearchUserByIdRequest;
use LaSalle\StudentTeacher\User\Shared\Application\Service\SearchUserByIdService;
use Symfony\Component\HttpFoundation\Response;

final class SearchUserController extends AbstractFOSRestController
{
    public function __construct(private SearchUserByIdService $searchUserByIdService)
    {
    }

    /**
     * @Rest\Get("/api/v1/users/{userId}")
     */
    public function getAction(string $userId): Response
    {
        $userResponse = ($this->searchUserByIdService)(new SearchUserByIdRequest($userId));
        return $this->handleView($this->view($userResponse, Response::HTTP_OK));
    }
}
