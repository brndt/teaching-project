<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\User\Application\Exception\InvalidConfirmationTokenException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\ConfirmUserEmailRequest;
use LaSalle\StudentTeacher\User\Application\Service\ConfirmUserEmail;

final class EmailConfirmationTokenController extends AbstractFOSRestController
{
    private ConfirmUserEmail $confirmUserEmail;

    public function __construct(ConfirmUserEmail $confirmUserEmail)
    {
        $this->confirmUserEmail = $confirmUserEmail;
    }

    /**
     * @Rest\Post("/api/v1/users/{id}/email_confirmation")
     * @QueryParam(name="token", strict=true, nullable=true)
     */
    public function postAction(ParamFetcher $paramFetcher, string $id)
    {
        $confirmationToken = $paramFetcher->get('token');

        try {
            ($this->confirmUserEmail)(new ConfirmUserEmailRequest($id, $confirmationToken));
        } catch (InvalidArgumentValidationException $e) {
        } catch (InvalidConfirmationTokenException $e) {
        } catch (UserNotFoundException $e) {
        }
    }
}