<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\User\Create\CreateUser;
use LaSalle\StudentTeacher\User\Application\User\Create\CreateUserRequest;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\Validator\Password;
use Symfony\Component\HttpFoundation\Response;

final class CreateUserController extends AbstractFOSRestController
{
    private CreateUser $createUser;

    public function __construct(CreateUser $createUser)
    {
        $this->createUser = $createUser;
    }

    /**
     * @Rest\Post("/api/sign_up", name="sign_up")
     * @RequestParam(name="username")
     * @RequestParam(name="password")
     * @RequestParam(name="firstName")
     * @RequestParam(name="lastName")
     * @RequestParam(name="roles")
     */
    public function postAction(ParamFetcher $paramFetcher): Response
    {
        $email = $paramFetcher->get('username');
        $password = $paramFetcher->get('password');
        $firstName = $paramFetcher->get('firstName');
        $lastName = $paramFetcher->get('lastName');
        $roles = $paramFetcher->get('roles');

        try {
            ($this->createUser)(
                new CreateUserRequest($email, $password, $firstName, $lastName, $roles)
            );
        } catch (UserAlreadyExistsException $e) {
            $view = $this->view(
                ['message' => 'User with this email already has been registered'],
                Response::HTTP_BAD_REQUEST
            );
            return $this->handleView($view);
        }

        $view = $this->view(['message' => 'User has been created'], Response::HTTP_OK);
        return $this->handleView($view);
    }

}