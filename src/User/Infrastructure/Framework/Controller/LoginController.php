<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Request\RequestRefreshToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class LoginController extends AbstractFOSRestController
{
    protected JWTTokenManagerInterface $jwtManager;
    private RefreshTokenManagerInterface $refreshTokenManager;
    private $ttl;
    private ValidatorInterface $validator;
    private RequestStack $requestStack;
    private $userIdentityField;
    private $tokenParameterName;
    private $singleUse;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        RefreshTokenManagerInterface $refreshTokenManager,
        $ttl,
        ValidatorInterface $validator,
        RequestStack $requestStack,
        $userIdentityField,
        $tokenParameterName,
        $singleUse
    ) {
        $this->jwtManager = $jwtManager;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->ttl = $ttl;
        $this->validator = $validator;
        $this->requestStack = $requestStack;
        $this->userIdentityField = $userIdentityField;
        $this->tokenParameterName = $tokenParameterName;
        $this->singleUse = $singleUse;
    }

    /**
     * @Rest\Post("/api/sign_in", name="sign_in")
     */
    public function postAction($jwt = null)
    {
        if (null === $jwt) {
            $jwt = $this->jwtManager->create($this->getUser());
        }
        $data['token'] = $jwt;

        $request = $this->requestStack->getCurrentRequest();

        $refreshTokenString = RequestRefreshToken::getRefreshToken($request, $this->tokenParameterName);

        if ($refreshTokenString && true === $this->singleUse) {
            $refreshToken = $this->refreshTokenManager->get($refreshTokenString);
            $refreshTokenString = null;

            if ($refreshToken instanceof RefreshTokenInterface) {
                $this->refreshTokenManager->delete($refreshToken);
            }
        }

        if ($refreshTokenString) {
            $data[$this->tokenParameterName] = $refreshTokenString;
        } else {
            $datetime = new \DateTime();
            $datetime->modify('+' . $this->ttl . ' seconds');

            $refreshToken = $this->refreshTokenManager->create();

            $accessor = new PropertyAccessor();
            $userIdentityFieldValue = $accessor->getValue($this->getUser(), $this->userIdentityField);

            $refreshToken->setUsername($userIdentityFieldValue);
            $refreshToken->setRefreshToken();
            $refreshToken->setValid($datetime);

            $valid = false;
            while (false === $valid) {
                $valid = true;
                $errors = $this->validator->validate($refreshToken);
                if ($errors->count() > 0) {
                    foreach ($errors as $error) {
                        if ('refreshToken' === $error->getPropertyPath()) {
                            $valid = false;
                            $refreshToken->setRefreshToken();
                        }
                    }
                }
            }

            $this->refreshTokenManager->save($refreshToken);
            $data[$this->tokenParameterName] = $refreshToken->getRefreshToken();
        }

        return $data;
    }
}