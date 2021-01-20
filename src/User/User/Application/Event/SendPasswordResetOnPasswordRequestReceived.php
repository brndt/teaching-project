<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Application\Event;

use LaSalle\StudentTeacher\User\User\Domain\EmailSender;
use LaSalle\StudentTeacher\User\User\Domain\Event\PasswordResetRequestReceivedDomainEvent;

final class SendPasswordResetOnPasswordRequestReceived
{
    public function __construct(private EmailSender $emailSender)
    {
    }

    public function __invoke(PasswordResetRequestReceivedDomainEvent $event): void
    {
        ($this->emailSender)->sendPasswordReset(
            $event->getEmail(),
            $event->getAggregateId(),
            $event->getFirstName(),
            $event->getLastName(),
            $event->getConfirmationToken()
        );
    }
}
