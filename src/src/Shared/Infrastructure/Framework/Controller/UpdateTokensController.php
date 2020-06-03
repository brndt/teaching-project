<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\Application\Request\UpdateRefreshTokenExpirationRequest;
use LaSalle\StudentTeacher\User\Application\Service\UpdateRefreshTokenExpirationService;
use Symfony\Component\HttpFoundation\Response;

final class UpdateTokensController extends AbstractFOSRestController
{
    private UpdateRefreshTokenExpirationService $updateRefreshTokenExpiration;

    public function __construct(UpdateRefreshTokenExpirationService $updateRefreshTokenExpiration)
    {
        $this->updateRefreshTokenExpiration = $updateRefreshTokenExpiration;
    }

    /**
     * @Rest\Post("/api/v1/users/token_refresh")
     * @RequestParam(name="refresh_token")
     */
    public function postAction(ParamFetcher $paramFetcher): Response
    {
        $refreshToken = $paramFetcher->get('refresh_token');

        $refreshTokensResponse = ($this->updateRefreshTokenExpiration)(
            new UpdateRefreshTokenExpirationRequest($refreshToken, new \DateTimeImmutable('+ 2592000 seconds'))
        );

        return $this->handleView($this->view($refreshTokensResponse, Response::HTTP_OK));
    }
}
