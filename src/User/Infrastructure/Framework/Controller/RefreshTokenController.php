<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenIsInvalidException;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\Exception\TokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\RefreshToken\Update\UpdateRefreshTokenValidationDateByTokenValue;
use LaSalle\StudentTeacher\Token\Application\RefreshToken\Update\UpdateRefreshTokenValidationDateByTokenValueRequest;
use LaSalle\StudentTeacher\Token\Application\Token\Create\CreateToken;
use LaSalle\StudentTeacher\Token\Application\Token\Create\CreateTokenRequest;
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

        $datetime = new \DateTime();
        $datetime->modify('+ 2592000 seconds');

        try {
            $refreshTokenResponse = ($this->updateRefreshToken)(
                new UpdateRefreshTokenValidationDateByTokenValueRequest($datetime, $refreshTokenValue)
            );
        } catch (RefreshTokenIsInvalidException $e) {
            $view = $this->view(['message' => 'Refresh token is not found'], Response::HTTP_NOT_FOUND);
            return $this->handleView($view);
        } catch (RefreshTokenNotFoundException $e) {
            $view = $this->view(['message' => 'Refresh token is expired'], Response::HTTP_BAD_REQUEST);
            return $this->handleView($view);
        }

        try {
            $tokenResponse = ($this->createToken)(new CreateTokenRequest($refreshTokenResponse->getUuid()));
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