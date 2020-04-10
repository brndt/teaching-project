<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use LaSalle\StudentTeacher\User\Application\CreateUser;
use LaSalle\StudentTeacher\User\Application\CreateUserRequest;
use LaSalle\StudentTeacher\User\Application\SearchUserByEmail;
use LaSalle\StudentTeacher\User\Application\SearchUserByEmailRequest;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\User\SymfonyUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiController
 *
 * @Route("/api")
 */
final class RegistrationHTMLController extends AbstractFOSRestController
{
    private CreateUser $createUser;
    private SearchUserByEmail $searchUser;

    public function __construct(CreateUser $createUser, SearchUserByEmail $searchUser)
    {
        $this->createUser = $createUser;
        $this->searchUser = $searchUser;
    }

    /**
     * @Rest\Post("/sign_up", name="sign_up")
     *
     * @SWG\Response(
     *     response=201,
     *     description="User was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="User was not successfully registered"
     * )
     *
     * @SWG\Parameter(
     *     name="_name",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="_email",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="_username",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="_password",
     *     in="query",
     *     type="string",
     *     description="The password"
     * )
     *
     * @SWG\Tag(name="User")
     */
    public function signUp(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $userRequestInfo = json_decode($request->getContent());

        $userResponse = $this->searchUser-__invoke>(new SearchUserByEmailRequest($userRequestInfo->email));

        if (null !== $userResponse) {
            return JsonResponse::create(['message' => 'This email is already in use'], Response::HTTP_BAD_REQUEST);
        }

        $symfonyUser = new SymfonyUser(
            $userRequestInfo->email,
            $userRequestInfo->password,
            $userRequestInfo->firstName,
            $userRequestInfo->lastName,
            $userRequestInfo->role
        );
        $encodedPassword = $encoder->encodePassword($symfonyUser, $userRequestInfo->password);

        $this->createUser->__invoke(
            new CreateUserRequest(
                $userRequestInfo->email,
                $encodedPassword,
                $userRequestInfo->firstName,
                $userRequestInfo->lastName,
                $userRequestInfo->role
            )
        );

        return JsonResponse::create(['message' => 'The user was successfully created'], Response::HTTP_OK);
    }
}