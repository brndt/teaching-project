<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\Application\Request\CreateUserRequest;
use LaSalle\StudentTeacher\User\Application\Service\CreateUserService;
use Symfony\Component\HttpFoundation\Response;

final class SignUpUserController extends AbstractFOSRestController
{
    public function __construct(private CreateUserService $createUser)
    {
    }

    /**
     * @Rest\Post("/api/v1/users")
     * @RequestParam(name="email")
     * @RequestParam(name="password")
     * @RequestParam(name="firstName")
     * @RequestParam(name="lastName")
     * @RequestParam(name="roles")
     */
    public function postAction(ParamFetcher $paramFetcher): Response
    {
        $email = $paramFetcher->get('email');
        $password = $paramFetcher->get('password');
        $firstName = $paramFetcher->get('firstName');
        $lastName = $paramFetcher->get('lastName');
        $roles = $paramFetcher->get('roles');

        ($this->createUser)(
            new CreateUserRequest($email, $password, $firstName, $lastName, $roles, new \DateTimeImmutable())
        );

        return $this->handleView($this->view(['message' => 'User has been created'], Response::HTTP_CREATED));
    }
}
