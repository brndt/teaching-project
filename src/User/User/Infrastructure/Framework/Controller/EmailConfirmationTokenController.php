<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\User\Application\Request\ConfirmUserEmailRequest;
use LaSalle\StudentTeacher\User\User\Application\Service\ConfirmUserEmailService;
use Symfony\Component\HttpFoundation\Response;

final class EmailConfirmationTokenController extends AbstractFOSRestController
{
    public function __construct(private ConfirmUserEmailService $confirmUserEmail)
    {
    }

    /**
     * @Rest\Post("/api/v1/users/{userId}/email_confirmation")
     * @RequestParam(name="token")
     */
    public function postAction(ParamFetcher $paramFetcher, string $userId): Response
    {
        $confirmationToken = $paramFetcher->get('token');

        ($this->confirmUserEmail)(new ConfirmUserEmailRequest($userId, $confirmationToken));

        return $this->handleView(
            $this->view(['message' => 'Your account has been successfully enabled'], Response::HTTP_OK)
        );
    }
}