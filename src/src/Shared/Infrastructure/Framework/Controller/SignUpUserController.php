<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyExistsException;
use LaSalle\StudentTeacher\User\Application\Request\CreateUserRequest;
use LaSalle\StudentTeacher\User\Application\Service\CreateUser;
use Symfony\Component\HttpFoundation\Response;

final class SignUpUserController extends AbstractFOSRestController
{
    private CreateUser $createUser;

    public function __construct(CreateUser $createUser)
    {
        $this->createUser = $createUser;
    }

    /**
     * @Rest\Post("/api/v1/users")
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
                new CreateUserRequest($email, $password, $firstName, $lastName, $roles, new \DateTimeImmutable())
            );
        } catch (UserAlreadyExistsException $e) {
            $view = $this->view(
                ['message' => 'User with this email already has been registered'],
                Response::HTTP_BAD_REQUEST
            );
            return $this->handleView($view);
        } catch (InvalidArgumentValidationException $error) {
            $view = $this->view(
                ['message' => $error->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
            return $this->handleView($view);
        }

        $view = $this->view(['message' => 'User has been created'], Response::HTTP_OK);
        return $this->handleView($view);
    }

}