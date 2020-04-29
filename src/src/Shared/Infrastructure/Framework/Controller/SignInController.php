<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Token\Application\Exception\TokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\Request\CreateTokenRequest;
use LaSalle\StudentTeacher\Token\Application\Request\SaveRefreshTokenRequest;
use LaSalle\StudentTeacher\Token\Application\Service\CreateToken;
use LaSalle\StudentTeacher\Token\Application\Service\SaveRefreshToken;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use Symfony\Component\HttpFoundation\Response;

final class SignInController extends AbstractFOSRestController
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
     * @Rest\Post("/api/v1/users/sign_in")
     */
    public function postAction()
    {
        if (false === $this->getUser()->getEnabled()) {
            $view = $this->view(['message' => 'You need to verify your email'], Response::HTTP_BAD_REQUEST);
            return $this->handleView($view);
        }

        try {
            $tokenResponse = ($this->createToken)(new CreateTokenRequest($this->getUser()->getId()));
        } catch (TokenNotFoundException $e) {
            $view = $this->view(['message' => 'Can\'t create token'], Response::HTTP_BAD_REQUEST);
            return $this->handleView($view);
        } catch (UserNotFoundException $e) {
            $view = $this->view(['message' => 'There\'s no user with such data'], Response::HTTP_NOT_FOUND);
            return $this->handleView($view);
        } catch (InvalidArgumentValidationException $error) {
            $view = $this->view(
                ['message' => $error->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
            return $this->handleView($view);
        }

        $dateTime = new \DateTime('+ 2592000 seconds');

        try {
            $refreshTokenResponse = ($this->saveRefreshToken)(
                new SaveRefreshTokenRequest($this->getUser()->getId(), $dateTime)
            );
        } catch (InvalidArgumentValidationException $error) {
            $view = $this->view(
                ['message' => $error->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
            return $this->handleView($view);
        }

        $view = $this->view(
            ['token' => $tokenResponse->getToken(), 'refresh_token' => $refreshTokenResponse->getRefreshToken()],
            Response::HTTP_OK
        );
        return $this->handleView($view);
    }
}