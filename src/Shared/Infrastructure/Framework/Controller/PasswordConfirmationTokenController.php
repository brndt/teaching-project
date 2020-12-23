<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\Application\Request\ConfirmUserPasswordResetRequest;
use LaSalle\StudentTeacher\User\Application\Service\ConfirmUserPasswordResetService;
use Symfony\Component\HttpFoundation\Response;

final class PasswordConfirmationTokenController extends AbstractFOSRestController
{
    public function __construct(private ConfirmUserPasswordResetService $confirmUserPasswordReset)
    {
    }

    /**
     * @Rest\Post("/api/v1/users/{userId}/password_resetting")
     * @RequestParam(name="newPassword")
     * @RequestParam(name="confirmationToken")
     */
    public function postAction(ParamFetcher $paramFetcher, string $userId): Response
    {
        $newPassword = $paramFetcher->get('newPassword');
        $confirmationToken = $paramFetcher->get('confirmationToken');

        ($this->confirmUserPasswordReset)(
            new ConfirmUserPasswordResetRequest($userId, $newPassword, $confirmationToken)
        );

        return $this->handleView(
            $this->view(['message' => 'Your password has been successfully changed'], Response::HTTP_OK)
        );
    }
}
