<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\Application\Request\ConfirmUserEmailRequest;
use LaSalle\StudentTeacher\User\Application\Request\ConfirmUserPasswordResetRequest;
use LaSalle\StudentTeacher\User\Application\Request\SendPasswordResetRequest;
use LaSalle\StudentTeacher\User\Application\Service\ConfirmUserEmail;
use LaSalle\StudentTeacher\User\Application\Service\ConfirmUserPasswordReset;
use Symfony\Component\HttpFoundation\Response;

final class PasswordConfirmationTokenController extends AbstractFOSRestController
{
    private ConfirmUserPasswordReset $confirmUserPasswordReset;

    public function __construct(ConfirmUserPasswordReset $confirmUserPasswordReset)
    {
        $this->confirmUserPasswordReset = $confirmUserPasswordReset;
    }

    /**
     * @Rest\Post("/api/v1/users/{userId}/password_resetting")
     * @RequestParam(name="new_password")
     * @RequestParam(name="confirmation_token")
     */
    public function postAction(ParamFetcher $paramFetcher, string $userId): Response
    {
        $newPassword = $paramFetcher->get('new_password');
        $confirmationToken = $paramFetcher->get('confirmation_token');

        ($this->confirmUserPasswordReset)(new ConfirmUserPasswordResetRequest($userId, $newPassword, $confirmationToken));

        return $this->handleView(
            $this->view(['message' => 'Your password has been successfully changed'], Response::HTTP_OK)
        );
    }
}