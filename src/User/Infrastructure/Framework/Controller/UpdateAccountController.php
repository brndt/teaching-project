<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\Application\SearchUserByEmail;
use LaSalle\StudentTeacher\User\Application\SearchUserByEmailRequest;
use LaSalle\StudentTeacher\User\Application\UpdateUser;
use LaSalle\StudentTeacher\User\Application\UpdateUserRequest;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\Entity\SymfonyUser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UpdateAccountController extends AbstractFOSRestController
{
    private UpdateUser $updateUser;
    private SearchUserByEmail $searchUser;

    public function __construct(UpdateUser $updateUser, SearchUserByEmail $searchUser)
    {
        $this->updateUser = $updateUser;
        $this->searchUser = $searchUser;
    }

    /**
     * @Rest\Put("/api/account/{id}")
     * @RequestParam(name="username")
     * @RequestParam(name="password")
     * @RequestParam(name="firstName")
     * @RequestParam(name="lastName")
     * @RequestParam(name="image")
     * @RequestParam(name="education")
     * @RequestParam(name="experience")
     */
    public function putAction(ParamFetcher $paramFetcher, int $id, UserPasswordEncoderInterface $encoder)
    {
        $email = $paramFetcher->get('username');
        $password = $paramFetcher->get('password');
        $firstName = $paramFetcher->get('firstName');
        $lastName = $paramFetcher->get('lastName');
        $image = $paramFetcher->get('image');
        $education = $paramFetcher->get('education');
        $experience = $paramFetcher->get('experience');

        if ($id !== $this->getUser()->getId()) {
            $view = $this->view(
                ['message' => 'You don\'t have permission to get profile'],
                Response::HTTP_FORBIDDEN
            );
            return $this->handleView($view);
        }

        $userResponse = $this->searchUser->__invoke(new SearchUserByEmailRequest($this->getUser()->getEmail()));

        if (null !== $userResponse && $email !== $userResponse->getEmail()) {
            $view = $this->view(
                ['message' => 'Your new email is already registered'],
                Response::HTTP_BAD_REQUEST
            );
            return $this->handleView($view);
        }

        $roles = $this->getUser()->getRoles();
        $encodedPassword = $encoder->encodePassword(new SymfonyUser(), $password);

        $userResponse = ($this->updateUser)(
            new UpdateUserRequest(
                $email, $encodedPassword, $firstName, $lastName, $roles, $id, $education, $experience, $image
            )
        );

        $view = $this->view($userResponse, Response::HTTP_OK);
        return $this->handleView($view);
    }
}