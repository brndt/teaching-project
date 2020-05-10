<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Event;

use LaSalle\StudentTeacher\User\Application\Request\SendEmailConfirmationRequest;
use LaSalle\StudentTeacher\User\Application\Service\SendEmailConfirmationService;
use LaSalle\StudentTeacher\User\Domain\Event\UserCreatedDomainEvent;

final class SendEmailConfirmationOnUserCreated
{
    private SendEmailConfirmationService $sendEmailConfirmation;

    public function __construct(SendEmailConfirmationService $sendEmailConfirmation)
    {
        $this->sendEmailConfirmation = $sendEmailConfirmation;
    }

    public function __invoke(UserCreatedDomainEvent $createdDomainEvent): void
    {
        ($this->sendEmailConfirmation)(new SendEmailConfirmationRequest($createdDomainEvent->getEmail()->toString()));
    }
}