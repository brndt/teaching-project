<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Token\Application\Request\GenerateTokensRequest;
use LaSalle\StudentTeacher\Token\Application\Service\CreateToken;
use LaSalle\StudentTeacher\Token\Application\Service\GenerateTokens;
use LaSalle\StudentTeacher\User\Application\Request\SignInRequest;
use LaSalle\StudentTeacher\User\Application\Service\SignIn;
use Symfony\Component\HttpFoundation\Response;

final class SignInController extends AbstractFOSRestController
{
    private SignIn $signIn;
    private GenerateTokens $generateTokens;

    public function __construct(SignIn $signIn, GenerateTokens $generateTokens)
    {
        $this->signIn = $signIn;
        $this->generateTokens = $generateTokens;
    }

    /**
     * @Rest\Post("/api/v1/users/sign_in")
     * @RequestParam(name="email")
     * @RequestParam(name="password")
     */
    public function postAction(ParamFetcher $paramFetcher): Response
    {
        $email = $paramFetcher->get('email');
        $password = $paramFetcher->get('password');

        $userResponse = ($this->signIn)(new SignInRequest($email, $password));

        $generateTokensResponse = ($this->generateTokens)(
            new GenerateTokensRequest($userResponse->getId(), new \DateTime('+ 2592000 seconds'))
        );

        return $this->handleView($this->view($generateTokensResponse, Response::HTTP_OK));
    }
}