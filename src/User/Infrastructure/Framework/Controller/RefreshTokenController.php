<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Security\Authenticator\RefreshTokenAuthenticator;
use Gesdinet\JWTRefreshTokenBundle\Security\Provider\RefreshTokenProvider;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\RefreshToken\Search\SearchRefreshTokenByTokenValue;
use LaSalle\StudentTeacher\Token\Application\RefreshToken\Update\UpdateRefreshTokenValidationDateByTokenValue;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

final class RefreshTokenController extends AbstractFOSRestController
{
    private UpdateRefreshTokenValidationDateByTokenValue $updateRefreshToken;

    public function __construct(UpdateRefreshTokenValidationDateByTokenValue $updateRefreshToken)
    {
        $this->updateRefreshToken = $updateRefreshToken;
    }

    /**
     * @Rest\Post("/api/refresh_token", name="token_refresh")
     * @RequestParam(name="refresh_token")
     */
    public function postAction(ParamFetcher $paramFetcher)
    {
        $refreshTokenValue = $paramFetcher->get('refresh_token');

        $datetime = new \DateTime();
        $datetime->modify('+ 2592000 seconds');

        try {
            $refreshToken = ($this->updateRefreshToken)($datetime, $refreshTokenValue);
        } catch (RefreshTokenNotFoundException $e) {
            $view = $this->view(
                ['code' => Response::HTTP_NOT_FOUND, 'message' => 'Provided Refresh token is invalid or expired'],
                Response::HTTP_NOT_FOUND
            );
            return $this->handleView($view);
        }

        $view = $this->view(
            ['code' => Response::HTTP_OK, 'message' => 'Refresh token has been successfully updated'],
            Response::HTTP_OK
        );
        return $this->handleView($view);

    }
}