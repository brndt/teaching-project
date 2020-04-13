<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\Application\User\Create\CreateUser;
use LaSalle\StudentTeacher\User\Application\User\Create\CreateUserRequest;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\Entity\SymfonyUser;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\Validator\Password;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\Validator\UniqueEmail;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class CreateUserController extends AbstractFOSRestController
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
     * @RequestParam(name="roles", map=true, requirements="ROLE_STUDENT|ROLE_TEACHER")
     */
    public function postAction(ParamFetcher $paramFetcher, UserPasswordEncoderInterface $encoder): Response
    {
        $username = $paramFetcher->get('username');
        $password = $paramFetcher->get('password');
        $firstName = $paramFetcher->get('firstName');
        $lastName = $paramFetcher->get('lastName');
        $roles = $paramFetcher->get('roles');
        $uuid = Uuid::uuid4()->toString();

        $encodedPassword = $encoder->encodePassword(new SymfonyUser(), $password);

        ($this->createUser)(
            new CreateUserRequest($username, $uuid, $encodedPassword, $firstName, $lastName, $roles)
        );

        $view = $this->view(
            ['message' => 'User has been created'],
            Response::HTTP_OK
        );
        return $this->handleView($view);
    }

}