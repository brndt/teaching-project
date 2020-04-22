<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenIsExpiredException;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\Exception\TokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\Request\CreateTokenRequest;
use LaSalle\StudentTeacher\Token\Application\Request\UpdateRefreshTokenValidationDateByTokenValueRequest;
use LaSalle\StudentTeacher\Token\Application\Service\CreateToken;
use LaSalle\StudentTeacher\Token\Application\Service\UpdateRefreshTokenValidationDateByTokenValue;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use Symfony\Component\HttpFoundation\Response;

final class RefreshTokenController extends AbstractFOSRestController
{
    private UpdateRefreshTokenValidationDateByTokenValue $updateRefreshToken;
    private CreateToken $createToken;

    public function __construct(
        UpdateRefreshTokenValidationDateByTokenValue $updateRefreshToken,
        CreateToken $createToken
    ) {
        $this->updateRefreshToken = $updateRefreshToken;
        $this->createToken = $createToken;
    }

    /**
     * @Rest\Post("/api/refresh_token", name="token_refresh")
     * @RequestParam(name="refresh_token")
     */
    public function postAction(ParamFetcher $paramFetcher)
    {
        $refreshTokenValue = $paramFetcher->get('refresh_token');

        $dateTime = new \DateTime('+ 2592000 seconds');

        try {
            $refreshTokenResponse = ($this->updateRefreshToken)(
                new UpdateRefreshTokenValidationDateByTokenValueRequest($dateTime, $refreshTokenValue)
            );
        } catch (RefreshTokenNotFoundException $e) {
            $view = $this->view(['message' => 'Refresh token is not found'], Response::HTTP_NOT_FOUND);
            return $this->handleView($view);
        } catch (RefreshTokenIsExpiredException $e) {
            $view = $this->view(['message' => 'Refresh token is expired'], Response::HTTP_BAD_REQUEST);
            return $this->handleView($view);
        }

        try {
            $tokenResponse = ($this->createToken)(new CreateTokenRequest($refreshTokenResponse->getUserId()));
        } catch (TokenNotFoundException $e) {
            $view = $this->view(['message' => 'Can\'t create token'], Response::HTTP_BAD_REQUEST);
            return $this->handleView($view);
        } catch (UserNotFoundException $e) {
            $view = $this->view(['message' => 'There\'s no user with such data'], Response::HTTP_NOT_FOUND);
            return $this->handleView($view);
        }

        $view = $this->view(
            ['token' => $tokenResponse->getToken(), 'refresh_token' => $refreshTokenResponse->getRefreshToken()],
            Response::HTTP_OK
        );
        return $this->handleView($view);
    }
}