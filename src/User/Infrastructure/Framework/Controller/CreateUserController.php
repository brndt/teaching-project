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
use LaSalle\StudentTeacher\User\Domain\Roles;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\Entity\SymfonyUser;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\Validator\Password;
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
     * @RequestParam(name="username")
     * @RequestParam(name="password", requirements=@Password)
     * @RequestParam(name="firstName")
     * @RequestParam(name="lastName")
     * @RequestParam(name="roles", map=true, requirements="ROLE_STUDENT|ROLE_TEACHER")
     */
    public function postAction(ParamFetcher $paramFetcher, UserPasswordEncoderInterface $encoder): Response
    {
        $email = $paramFetcher->get('username');
        $password = $paramFetcher->get('password');
        $firstName = $paramFetcher->get('firstName');
        $lastName = $paramFetcher->get('lastName');
        $roles = $paramFetcher->get('roles');
        $uuid = Uuid::uuid4()->toString();

        $encodedPassword = $encoder->encodePassword(
            new SymfonyUser(
                $uuid,
                $email,
                $password,
                $firstName,
                $lastName,
                Roles::fromPrimitives($roles),
                new \DateTimeImmutable()
            ),
            $password
        );

        try {
            ($this->createUser)(
                new CreateUserRequest($email, $uuid, $encodedPassword, $firstName, $lastName, $roles)
            );
        } catch (UserAlreadyExistsException $e) {
            $view = $this->view(
                ['code' => Response::HTTP_BAD_REQUEST, 'message' => 'User with this email already has been registered'],
                Response::HTTP_BAD_REQUEST
            );
            return $this->handleView($view);
        }

        $view = $this->view(
            ['code' => Response::HTTP_OK, 'message' => 'User has been created'],
            Response::HTTP_OK
        );
        return $this->handleView($view);
    }

}