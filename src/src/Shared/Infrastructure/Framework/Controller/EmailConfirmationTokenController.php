<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\User\Application\Exception\InvalidConfirmationTokenException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
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
    public function postAction(ParamFetcher $paramFetcher, string $id)
    {
        $confirmationToken = $paramFetcher->get('token');

        try {
            ($this->confirmUserEmail)(new ConfirmUserEmailRequest($id, $confirmationToken));
        } catch (InvalidArgumentValidationException $exception) {
            $view = $this->view(
                ['message' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
            return $this->handleView($view);
        } catch (InvalidConfirmationTokenException $e) {
            $view = $this->view(['message' => 'Confirmation token is invalid'], Response::HTTP_BAD_REQUEST);
            return $this->handleView($view);
        } catch (UserNotFoundException $e) {
            $view = $this->view(['message' => 'There\'s no user with such data'], Response::HTTP_BAD_REQUEST);
            return $this->handleView($view);
        }

        $view = $this->view(['message' => 'Your account has been successfully enabled'], Response::HTTP_OK);
        return $this->handleView($view);
    }
}