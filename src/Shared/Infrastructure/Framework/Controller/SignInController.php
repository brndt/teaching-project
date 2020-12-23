<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use DateTimeImmutable;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\Application\Request\GenerateTokensRequest;
use LaSalle\StudentTeacher\User\Application\Request\SignInRequest;
use LaSalle\StudentTeacher\User\Application\Service\GenerateTokensService;
use LaSalle\StudentTeacher\User\Application\Service\SearchUsersByCriteriaService;
use LaSalle\StudentTeacher\User\Application\Service\SignInService;
use Symfony\Component\HttpFoundation\Response;

final class SignInController extends AbstractFOSRestController
{
    public function __construct(
        private SignInService $signIn,
        private GenerateTokensService $generateTokens,
        private SearchUsersByCriteriaService $searchUsersByCriteriaService
    ) {
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
            new GenerateTokensRequest($userResponse->getId(), new DateTimeImmutable('+ 2592000 seconds'))
        );

        $response = array_merge(
            $userResponse->toPrimitives(),
            [
                'email' => $email,
                'token' => $generateTokensResponse->getToken(),
                'refreshToken' => $generateTokensResponse->getRefreshToken()
            ]
        );

        return $this->handleView($this->view($response, Response::HTTP_OK));
    }
}
