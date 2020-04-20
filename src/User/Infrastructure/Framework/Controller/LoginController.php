<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\Token\Application\Exception\TokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\RefreshToken\Save\SaveRefreshToken;
use LaSalle\StudentTeacher\Token\Application\RefreshToken\Save\SaveRefreshTokenRequest;
use LaSalle\StudentTeacher\Token\Application\Token\Create\CreateToken;
use LaSalle\StudentTeacher\Token\Application\Token\Create\CreateTokenRequest;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use Symfony\Component\HttpFoundation\Response;

final class LoginController extends AbstractFOSRestController
{
    private CreateToken $createToken;
    private SaveRefreshToken $saveRefreshToken;

    public function __construct(
        CreateToken $createToken,
        SaveRefreshToken $saveRefreshToken
    ) {
        $this->createToken = $createToken;
        $this->saveRefreshToken = $saveRefreshToken;
    }

    /**
     * @Rest\Post("/api/sign_in", name="sign_in")
     */
    public function postAction()
    {
        try {
            $tokenResponse = ($this->createToken)(new CreateTokenRequest($this->getUser()->getId()->getValue()));
        } catch (TokenNotFoundException $e) {
            $view = $this->view(['message' => 'Can\'t create token'], Response::HTTP_BAD_REQUEST);
            return $this->handleView($view);
        } catch (UserNotFoundException $e) {
            $view = $this->view(['message' => 'There\'s no user with such data'], Response::HTTP_NOT_FOUND);
            return $this->handleView($view);
        }

        $dateTime = new \DateTime('+ 2592000 seconds');

        $refreshTokenResponse = ($this->saveRefreshToken)(
            new SaveRefreshTokenRequest($this->getUser()->getId()->getValue(), $dateTime)
        );

        $view = $this->view(
            ['token' => $tokenResponse->getToken(), 'refresh_token' => $refreshTokenResponse->getRefreshToken()],
            Response::HTTP_OK
        );
        return $this->handleView($view);
    }
}