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
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Component\HttpFoundation\Response;

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
     * @Rest\Put("/api/update_account/{id}", name="update_account")
     * @RequestParam(name="token")
     * @RequestParam(name="username")
     * @RequestParam(name="password")
     * @RequestParam(name="firstName")
     * @RequestParam(name="lastName")
     * @RequestParam(name="role", requirements="student|teacher|admin")
     * @RequestParam(name="image")
     * @RequestParam(name="education")
     * @RequestParam(name="experience")
     * @throws JWTDecodeFailureException
     */
    public function signUp(ParamFetcher $paramFetcher, int $id, JWTEncoderInterface $encoder)
    {
        $token = $paramFetcher->get('token');
        $username = $paramFetcher->get('username');
        $password = $paramFetcher->get('password');
        $firstName = $paramFetcher->get('firstName');
        $lastName = $paramFetcher->get('lastName');
        $role = $paramFetcher->get('role');
        $image = $paramFetcher->get('image');
        $education = $paramFetcher->get('education');
        $experience = $paramFetcher->get('experience');

        $decodedToken = $encoder->decode($token);

        $userResponse = $this->searchUser->__invoke(new SearchUserByEmailRequest($decodedToken['username']));

        if ($id !== $userResponse->getId()) {
            $view = $this->view(
                ['message' => 'You don\'t have permission to update profile'],
                Response::HTTP_FORBIDDEN
            );
            return $this->handleView($view);
        }

        if ($role !== $userResponse->getRole()) {
            $view = $this->view(
                ['message' => 'You can\'t change your account status'],
                Response::HTTP_FORBIDDEN
            );
            return $this->handleView($view);
        }

        $userResponse = ($this->updateUser)(
            new UpdateUserRequest(
                $username,
                $password,
                $firstName,
                $lastName,
                $role,
                $id,
                $education,
                $experience,
                $image
            )
        );

        $view = $this->view($userResponse, Response::HTTP_OK);
        return $this->handleView($view);
    }
}