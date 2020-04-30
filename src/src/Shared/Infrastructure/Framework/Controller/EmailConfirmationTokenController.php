<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\Application\Request\ConfirmUserEmailRequest;
use LaSalle\StudentTeacher\User\Application\Service\ConfirmUserEmail;
use Symfony\Component\HttpFoundation\Response;

final class EmailConfirmationTokenController extends AbstractFOSRestController
{
    private ConfirmUserEmail $confirmUserEmail;

    public function __construct(ConfirmUserEmail $confirmUserEmail)
    {
        $this->confirmUserEmail = $confirmUserEmail;
    }

    /**
     * @Rest\Post("/api/v1/users/{id}/email_confirmation")
     * @RequestParam(name="token")
     */
    public function postAction(ParamFetcher $paramFetcher, string $id): Response
    {
        $confirmationToken = $paramFetcher->get('token');

        ($this->confirmUserEmail)(new ConfirmUserEmailRequest($id, $confirmationToken));

        return $this->handleView(
            $this->view(['message' => 'Your account has been successfully enabled'], Response::HTTP_OK)
        );
    }
}