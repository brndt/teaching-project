<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\RefreshToken\Application\Request\DeleteRefreshTokenRequest;
use LaSalle\StudentTeacher\User\RefreshToken\Application\Service\DeleteRefreshTokenService;
use Symfony\Component\HttpFoundation\Response;

final class SignOutController extends AbstractFOSRestController
{
    public function __construct(private DeleteRefreshTokenService $deleteRefreshToken)
    {
    }

    /**
     * @Rest\Delete("/api/v1/users/sign_out")
     * @RequestParam(name="refresh_token")
     */
    public function deleteAction(ParamFetcher $paramFetcher): Response
    {
        $refreshToken = $paramFetcher->get('refresh_token');

        ($this->deleteRefreshToken)(new DeleteRefreshTokenRequest($refreshToken));

        return $this->handleView($this->view(['message' => 'You have successfully signed out'], Response::HTTP_OK));
    }

}
