<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\Application\CreateUser;
use LaSalle\StudentTeacher\User\Application\CreateUserRequest;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\User\SymfonyUser;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\Validator\Password;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\Validator\UniqueEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


final class RegistrationController extends AbstractFOSRestController
{
    private CreateUser $createUser;

    public function __construct(CreateUser $createUser)
    {
        $this->createUser = $createUser;
    }

    /**
     * @Rest\Post("/api/sign_up", name="sign_up")
     * @RequestParam(name="username", requirements=@UniqueEmail)
     * @RequestParam(name="password", requirements=@Password)
     * @RequestParam(name="firstName")
     * @RequestParam(name="lastName")
     * @RequestParam(name="role", requirements="student|teacher")
     */
    public function signUp(ParamFetcher $paramFetcher, UserPasswordEncoderInterface $encoder): Response
    {
        $username = $paramFetcher->get('username');
        $password = $paramFetcher->get('password');
        $firstName = $paramFetcher->get('firstName');
        $lastName = $paramFetcher->get('lastName');
        $role = $paramFetcher->get('role');

        $symfonyUser = new SymfonyUser($username, $password, $firstName, $lastName, $role);

        $encodedPassword = $encoder->encodePassword($symfonyUser, $password);

        $userResponse = $this->createUser->__invoke(
            new CreateUserRequest($username, $encodedPassword, $firstName, $lastName, $role)
        );

        $view = $this->view($userResponse, Response::HTTP_OK);
        return $this->handleView($view);
    }
}