<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Application\Event;

use LaSalle\StudentTeacher\User\User\Domain\EmailSender;
use LaSalle\StudentTeacher\User\User\Domain\Event\EmailConfirmationRequestReceivedDomainEvent;

final class SendEmailConfirmationOnEmailConfirmationRequestReceived
{
    public function __construct(private EmailSender $emailSender)
    {
    }

    public function __invoke(EmailConfirmationRequestReceivedDomainEvent $event): void
    {
        ($this->emailSender)->sendEmailConfirmation(
            $event->getEmail(),
            $event->getAggregateId(),
            $event->getFirstName(),
            $event->getLastName(),
            $event->getConfirmationToken()
        );
    }
}
