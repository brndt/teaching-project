<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\Request\DeleteRefreshTokenRequest;
use LaSalle\StudentTeacher\Token\Application\Service\DeleteRefreshToken;
use Symfony\Component\HttpFoundation\Response;

final class SignOutController extends AbstractFOSRestController
{
    private DeleteRefreshToken $deleteRefreshToken;

    public function __construct(DeleteRefreshToken $deleteRefreshToken)
    {
        $this->deleteRefreshToken = $deleteRefreshToken;
    }

    /**
     * @Rest\Delete("/api/v1/users/sign_out")
     * @RequestParam(name="refresh_token")
     */
    public function deleteAction(ParamFetcher $paramFetcher)
    {
        $refreshTokenValue = $paramFetcher->get('refresh_token');

        try {
            ($this->deleteRefreshToken)(new DeleteRefreshTokenRequest($refreshTokenValue));
        } catch (RefreshTokenNotFoundException $e) {
            $view = $this->view(
                ['code' => Response::HTTP_NOT_FOUND, 'message' => 'Refresh token is not found'],
                Response::HTTP_NOT_FOUND
            );
            return $this->handleView($view);
        }

        $view = $this->view(
            ['code' => Response::HTTP_OK, 'message' => 'Refresh token has been deleted'],
            Response::HTTP_OK
        );
        return $this->handleView($view);
    }

}