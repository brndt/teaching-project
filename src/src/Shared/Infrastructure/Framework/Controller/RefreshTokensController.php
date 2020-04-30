<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Token\Application\Request\RefreshTokensRequest;
use LaSalle\StudentTeacher\Token\Application\Service\UpdateRefreshTokensExpiration;
use Symfony\Component\HttpFoundation\Response;

final class RefreshTokensController extends AbstractFOSRestController
{
    private UpdateRefreshTokensExpiration $refreshTokens;

    public function __construct(UpdateRefreshTokensExpiration $refreshTokens)
    {
        $this->refreshTokens = $refreshTokens;
    }

    /**
     * @Rest\Post("/api/v1/users/token_refresh")
     * @RequestParam(name="refresh_token")
     */
    public function postAction(ParamFetcher $paramFetcher): Response
    {
        $refreshTokenValue = $paramFetcher->get('refresh_token');

        $refreshTokensResponse = ($this->refreshTokens)(
            new RefreshTokensRequest($refreshTokenValue, new \DateTime('+ 2592000 seconds'))
        );

        return $this->handleView($this->view($refreshTokensResponse, Response::HTTP_OK));
    }
}