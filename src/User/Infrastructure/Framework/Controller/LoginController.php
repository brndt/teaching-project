<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\Token\Application\RefreshToken\Save\SaveRefreshToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Response;

final class LoginController extends AbstractFOSRestController
{
    private JWTTokenManagerInterface $jwtManager;
    private SaveRefreshToken $saveRefreshToken;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        SaveRefreshToken $saveRefreshToken
    ) {
        $this->jwtManager = $jwtManager;
        $this->saveRefreshToken = $saveRefreshToken;
    }

    /**
     * @Rest\Post("/api/sign_in", name="sign_in")
     */
    public function postAction($jwt = null)
    {
        if (null === $jwt) {
            $jwt = $this->jwtManager->create($this->getUser());
        }

        $datetime = new \DateTime();
        $datetime->modify('+ 2592000 seconds');

        $refreshTokenResponse = $this->saveRefreshToken->__invoke($this->getUser()->getUuid(), $datetime);

        $view = $this->view(
            ['token' => $jwt, 'refresh_token' => $refreshTokenResponse->getRefreshToken()],
            Response::HTTP_OK
        );
        return $this->handleView($view);
    }
}