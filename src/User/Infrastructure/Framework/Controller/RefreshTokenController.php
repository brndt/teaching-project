<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Gesdinet\JWTRefreshTokenBundle\Service\RefreshToken;
use Symfony\Component\HttpFoundation\Request;

final class RefreshTokenController extends AbstractFOSRestController
{
    /**
     * @Rest\Post("/api/refresh_token", name="token_refresh")
     */
    public function refreshToken(Request $request, RefreshToken $refreshToken)
    {
        return $refreshToken->refresh($request);
    }
}