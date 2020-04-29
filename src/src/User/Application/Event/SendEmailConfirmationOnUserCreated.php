<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Event;

use LaSalle\StudentTeacher\User\Application\Service\SendEmailConfirmation;
use LaSalle\StudentTeacher\User\Application\Service\SendEmailConfirmationRequest;
use LaSalle\StudentTeacher\User\Domain\Event\UserCreatedDomainEvent;

final class SendEmailConfirmationOnUserCreated
{
    private SendEmailConfirmation $sendEmailConfirmation;

    public function __construct(SendEmailConfirmation $sendEmailConfirmation)
    {
        $this->sendEmailConfirmation = $sendEmailConfirmation;
    }

    public function __invoke(UserCreatedDomainEvent $createdDomainEvent): void
    {
        ($this->sendEmailConfirmation)(new SendEmailConfirmationRequest($createdDomainEvent->getEmail()->toString()));
    }
}