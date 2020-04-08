<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use LaSalle\StudentTeacher\User\Application\CreateUser;
use LaSalle\StudentTeacher\User\Application\CreateUserRequest;
use LaSalle\StudentTeacher\User\Application\SearchUserByEmail;
use LaSalle\StudentTeacher\User\Application\SearchUserByEmailRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RegistrationHTMLController
{
    private CreateUser $createUser;
    private SearchUserByEmail $searchUser;

    public function __construct(CreateUser $createUser, SearchUserByEmail $searchUser)
    {
        $this->createUser = $createUser;
        $this->searchUser = $searchUser;
    }

    /**
     * @Route("/api/sign_up", name="sign_up", methods={"POST"})
     */
    public function signUp(Request $request)
    {
        $response = new Response();
        $userInfo = json_decode($request->getContent());

        $user = $this->searchUser->__invoke(new SearchUserByEmailRequest($userInfo->email));

        if (null !== $user) {
            $response->setContent(
                json_encode(
                    [
                        'message' => 'This email is already in use',
                        'code' => 400
                    ]
                )
            );
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $this->createUser->__invoke(
            new CreateUserRequest(
                $userInfo->email,
                $userInfo->password,
                $userInfo->firstName,
                $userInfo->lastName,
                $userInfo->role
            )
        );

        $response->setContent(
            json_encode(
                [
                    'message' => 'The user was successfully created',
                    'code' => 200
                ]
            )
        );
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}