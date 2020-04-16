<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\User\Application\BasicUserInformation\Search\SearchBasicUserInformationById;
use LaSalle\StudentTeacher\User\Application\BasicUserInformation\Search\SearchBasicUserInformationByIdRequest;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

final class SearchBasicUserInformationController extends AbstractFOSRestController
{
    private SearchBasicUserInformationById $searchUser;

    public function __construct(SearchBasicUserInformationById $searchUser)
    {
        $this->searchUser = $searchUser;
    }

    /**
     * @Rest\Get("/api/account/{id}")
     */
    public function getAction(int $id, LoggerInterface $logger)
    {
        try {
            $userResponse = ($this->searchUser)(new SearchBasicUserInformationByIdRequest($id));
        } catch (UserNotFoundException $e) {
            $view = $this->view(
                ['code' => Response::HTTP_NOT_FOUND, 'message' => 'There\'s no user with such id'],
                Response::HTTP_NOT_FOUND
            );
            return $this->handleView($view);
        }

        $view = $this->view($userResponse, Response::HTTP_OK);
        return $this->handleView($view);
    }
}