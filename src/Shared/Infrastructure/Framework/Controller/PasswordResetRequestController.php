<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\Application\Request\SendPasswordResetRequest;
use LaSalle\StudentTeacher\User\Application\Service\SendPasswordResetService;
use Symfony\Component\HttpFoundation\Response;

final class PasswordResetRequestController extends AbstractFOSRestController
{
    public function __construct(private SendPasswordResetService $sendPasswordReset)
    {
    }

    /**
     * @Rest\Post("/api/v1/users/password_resetting")
     * @RequestParam(name="email")
     */
    public function postAction(ParamFetcher $paramFetcher): Response
    {
        $email = $paramFetcher->get('email');

        ($this->sendPasswordReset)(new SendPasswordResetRequest($email));

        return $this->handleView(
            $this->view(
                ['message' => 'An email has been sent. It contains a link you must click to reset your password.'],
                Response::HTTP_OK
            )
        );
    }
}