<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyEnabledException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Service\SendEmailConfirmation;
use LaSalle\StudentTeacher\User\Application\Service\SendEmailConfirmationRequest;
use Symfony\Component\HttpFoundation\Response;

final class EmailConfirmationRequestController extends AbstractFOSRestController
{
    private SendEmailConfirmation $sendEmailConfirmation;

    public function __construct(SendEmailConfirmation $sendEmailConfirmation)
    {
        $this->sendEmailConfirmation = $sendEmailConfirmation;
    }

    /**
     * @Rest\Post("/api/v1/users/email_confirmation")
     * @RequestParam(name="email")
     */
    public function postAction(ParamFetcher $paramFetcher)
    {
        $email = $paramFetcher->get('email');

        try {
            ($this->sendEmailConfirmation)(new SendEmailConfirmationRequest($email));
        } catch (InvalidArgumentValidationException $exception) {
            $view = $this->view(
                ['message' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
            return $this->handleView($view);
        } catch (UserAlreadyEnabledException $exception) {
            $view = $this->view(
                ['message' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
            return $this->handleView($view);
        } catch (UserNotFoundException $exception) {
            $view = $this->view(
                ['message' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
            return $this->handleView($view);
        }

        $view = $this->view(['message' => 'Confirmation has been successfully sent to your email'], Response::HTTP_OK);
        return $this->handleView($view);
    }
}