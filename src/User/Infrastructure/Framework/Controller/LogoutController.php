<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\RefreshToken\Delete\DeleteRefreshTokenByTokenValue;
use LaSalle\StudentTeacher\Token\Application\RefreshToken\Delete\DeleteRefreshTokenByTokenValueRequest;
use Symfony\Component\HttpFoundation\Response;

final class LogoutController extends AbstractFOSRestController
{
    private DeleteRefreshTokenByTokenValue $deleteRefreshToken;

    public function __construct(DeleteRefreshTokenByTokenValue $deleteRefreshToken)
    {
        $this->deleteRefreshToken = $deleteRefreshToken;
    }

    /**
     * @Rest\Delete("/api/log_out", name="log_out")
     * @RequestParam(name="refresh_token")
     */
    public function deleteAction(ParamFetcher $paramFetcher)
    {
        $refreshTokenValue = $paramFetcher->get('refresh_token');

        try {
            ($this->deleteRefreshToken)(new DeleteRefreshTokenByTokenValueRequest($refreshTokenValue));
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