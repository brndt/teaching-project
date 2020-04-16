<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\HttpFoundation\Response;

final class LogoutController extends AbstractFOSRestController
{
    /**
     * @Rest\Delete("/api/log_out", name="log_out")
     */
    public function deleteAction(RefreshTokenManagerInterface $tokenManager)
    {
        $token = $tokenManager->getLastFromUsername($this->getUser()->getUuid());

        if (null === $token) {
            $view = $this->view(
                ['code' => Response::HTTP_NOT_FOUND, 'message' => 'There\'s no refresh token found by this user'],
                Response::HTTP_NOT_FOUND
            );
            return $this->handleView($view);
        }

        $tokenManager->delete($token);

        $view = $this->view(
            ['code' => Response::HTTP_OK, 'message' => 'Refresh token has been deleted'],
            Response::HTTP_OK
        );
        return $this->handleView($view);
    }
}