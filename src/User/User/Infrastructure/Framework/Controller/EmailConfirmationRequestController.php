<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\User\Application\Request\SendEmailConfirmationRequest;
use LaSalle\StudentTeacher\User\User\Application\Service\SendEmailConfirmationService;
use Symfony\Component\HttpFoundation\Response;

final class EmailConfirmationRequestController extends AbstractFOSRestController
{
    public function __construct(private SendEmailConfirmationService $sendEmailConfirmation)
    {
    }

    /**
     * @Rest\Post("/api/v1/users/email_confirmation")
     * @RequestParam(name="email")
     */
    public function postAction(ParamFetcher $paramFetcher): Response
    {
        $email = $paramFetcher->get('email');

        ($this->sendEmailConfirmation)(new SendEmailConfirmationRequest($email));

        return $this->handleView(
            $this->view(['message' => 'Confirmation has been successfully sent to your email'], Response::HTTP_OK)
        );
    }
}