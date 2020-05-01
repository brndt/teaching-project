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
     * @Rest\Post("/api/v1/users/{id}/password_resetting")
     * @RequestParam(name="newPassword")
     * @RequestParam(name="token")
     */
    public function postAction(ParamFetcher $paramFetcher, string $id): Response
    {
        $newPassword = $paramFetcher->get('newPassword');
        $confirmationToken = $paramFetcher->get('token');

        ($this->confirmUserPasswordReset)(new ConfirmUserPasswordResetRequest($id, $newPassword, $confirmationToken));

        return $this->handleView(
            $this->view(['message' => 'Your password has been successfully changed'], Response::HTTP_OK)
        );
    }
}