<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Event;

use LaSalle\StudentTeacher\User\Application\Request\SendEmailConfirmationRequest;
use LaSalle\StudentTeacher\User\Application\Service\SendEmailConfirmationService;
use LaSalle\StudentTeacher\User\Domain\EmailSender;
use LaSalle\StudentTeacher\User\Domain\Event\UserCreatedDomainEvent;

final class SendEmailConfirmationOnUserCreated
{
    private EmailSender $emailSender;

    public function __construct(EmailSender $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    public function __invoke(UserCreatedDomainEvent $event): void
    {
        ($this->emailSender)->sendEmailConfirmation($event->getEmail(), $event->getAggregateId(), $event->getFirstName(), $event->getLastName(), $event->getConfirmationToken());
    }
}
