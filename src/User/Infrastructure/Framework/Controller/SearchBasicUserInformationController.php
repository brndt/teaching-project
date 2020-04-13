<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\User\Application\BasicUserInformation\Search\SearchBasicUserInformationById;
use LaSalle\StudentTeacher\User\Application\BasicUserInformation\Search\SearchBasicUserInformationByIdRequest;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
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
    public function getAction(int $id)
    {
        $userResponse = ($this->searchUser)(new SearchBasicUserInformationByIdRequest($id));

        if (null === $userResponse) {
            $view = $this->view(
                ['message' => 'Can\'t find user with this id'],
                Response::HTTP_FORBIDDEN
            );
            return $this->handleView($view);
        }

        $view = $this->view($userResponse, Response::HTTP_OK);
        return $this->handleView($view);
    }
}