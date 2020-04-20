<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\User\Application\BasicUserInformation\Search\SearchBasicUserInformation;
use LaSalle\StudentTeacher\User\Application\BasicUserInformation\Search\SearchBasicUserInformationRequest;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use Symfony\Component\HttpFoundation\Response;

final class SearchBasicUserInformationController extends AbstractFOSRestController
{
    private SearchBasicUserInformation $searchUser;

    public function __construct(SearchBasicUserInformation $searchUser)
    {
        $this->searchUser = $searchUser;
    }

    /**
     * @Rest\Get("/api/account/{id}")
     */
    public function getAction(string $id)
    {
        try {
            $userResponse = ($this->searchUser)(new SearchBasicUserInformationRequest($id));
        } catch (UserNotFoundException $e) {
            $view = $this->view(['message' => 'There\'s no user with such id'], Response::HTTP_NOT_FOUND);
            return $this->handleView($view);
        }

        $view = $this->view($userResponse, Response::HTTP_OK);
        return $this->handleView($view);
    }
}