<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Security\Authenticator\RefreshTokenAuthenticator;
use Gesdinet\JWTRefreshTokenBundle\Security\Provider\RefreshTokenProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

final class RefreshTokenController extends AbstractFOSRestController
{
    private RefreshTokenAuthenticator $authenticator;
    private RefreshTokenProvider $provider;
    private AuthenticationSuccessHandlerInterface $successHandler;
    private AuthenticationFailureHandlerInterface $failureHandler;
    private RefreshTokenManagerInterface $refreshTokenManager;
    private $ttl;
    private $providerKey;
    private $ttlUpdate;

    public function __construct(
        RefreshTokenAuthenticator $authenticator,
        RefreshTokenProvider $provider,
        AuthenticationSuccessHandlerInterface $successHandler,
        AuthenticationFailureHandlerInterface $failureHandler,
        RefreshTokenManagerInterface $refreshTokenManager,
        $ttl,
        $providerKey,
        $ttlUpdate
    ) {
        $this->authenticator = $authenticator;
        $this->provider = $provider;
        $this->successHandler = $successHandler;
        $this->failureHandler = $failureHandler;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->ttl = $ttl;
        $this->providerKey = $providerKey;
        $this->ttlUpdate = $ttlUpdate;
    }

    /**
     * @Rest\Post("/api/refresh_token", name="token_refresh")
     */
    public function postAction(Request $request)
    {
        try {
            $user = $this->authenticator->getUser(
                $this->authenticator->getCredentials($request),
                $this->provider
            );

            $postAuthenticationToken = $this->authenticator->createAuthenticatedToken($user, $this->providerKey);
        } catch (AuthenticationException $e) {
            return $this->failureHandler->onAuthenticationFailure($request, $e);
        }

        $credentials = $this->authenticator->getCredentials($request);
        $refreshToken = $this->refreshTokenManager->get($credentials['token']);

        if (null === $refreshToken || !$refreshToken->isValid()) {
            return $this->failureHandler->onAuthenticationFailure(
                $request,
                new AuthenticationException(
                    sprintf('Refresh token "%s" is invalid.', $refreshToken)
                )
            );
        }

        if ($this->ttlUpdate) {
            $expirationDate = new \DateTime();
            $expirationDate->modify(sprintf('+%d seconds', $this->ttl));
            $refreshToken->setValid($expirationDate);

            $this->refreshTokenManager->save($refreshToken);
        }

        return $this->successHandler->onAuthenticationSuccess($request, $postAuthenticationToken);
    }
}