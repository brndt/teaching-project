<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Event;

use LaSalle\StudentTeacher\User\Domain\EmailSender;
use LaSalle\StudentTeacher\User\Domain\Event\EmailConfirmationRequestReceivedDomainEvent;

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
