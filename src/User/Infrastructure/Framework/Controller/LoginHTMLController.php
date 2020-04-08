<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use LaSalle\GroupOne\Logging\Application\LogEntryResponse;
use LaSalle\StudentTeacher\User\Application\SearchUserByEmail;
use LaSalle\StudentTeacher\User\Application\SearchUserByEmailRequest;
use LaSalle\StudentTeacher\User\Application\UserResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class LoginHTMLController
{
    private SearchUserByEmail $searchUser;

    public function __construct(SearchUserByEmail $searchUser)
    {
        $this->searchUser = $searchUser;
    }

    /**
     * @Route("/api/sign_in", name="sign_in", methods={"POST"})
     */
    public function signIn(Request $request)
    {
        $response = new Response();
        $userInfo = json_decode($request->getContent());

        $userResponse = $this->searchUser->__invoke(new SearchUserByEmailRequest($userInfo->email));

        if (null == $userResponse) {
            $response->setContent(
                json_encode(
                    [
                        'message' => 'Can\'t find user by this email',
                        'code' => 404
                    ]
                )
            );
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $response->setContent(
            json_encode(
                [
                    'user' => $this->transformUserResponseToArray($userResponse),
                    'code' => 200
                ]
            )
        );
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    private function transformUserResponseToArray(UserResponse $user): array
    {
        return [
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'role' => $user->getRole(),
            'id' => $user->getId(),
            'image' => $user->getImage(),
            'education' => $user->getEducation(),
            'experience' => $user->getExperience(),
            'created' => $user->getCreated()
        ];
    }
}