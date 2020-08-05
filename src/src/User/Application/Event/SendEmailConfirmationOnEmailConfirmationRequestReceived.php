<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Event;

use CodelyTv\Mooc\Courses\Domain\CourseCreatedDomainEvent;
use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEventSubscriber;
use LaSalle\StudentTeacher\User\Domain\EmailSender;
use LaSalle\StudentTeacher\User\Domain\Event\EmailConfirmationRequestReceivedDomainEvent;

final class SendEmailConfirmationOnEmailConfirmationRequestReceived implements DomainEventSubscriber
{
    private EmailSender $emailSender;

    public function __construct(EmailSender $emailSender)
    {
        $this->emailSender = $emailSender;
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

    public static function subscribedTo(): array
    {
        return [EmailConfirmationRequestReceivedDomainEvent::class];
    }
}
