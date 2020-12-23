<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Event;

use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEvent;

final class EmailConfirmationRequestReceivedDomainEvent extends DomainEvent
{
    public function __construct(
        string $aggregateId,
        private string $email,
        private string $firstName,
        private string $lastName,
        private string $confirmationToken

    ) {
        parent::__construct($aggregateId);
    }

    public function getConfirmationToken(): string
    {
        return $this->confirmationToken;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public static function eventName(): string
    {
        return 'email.confirmation.request.received';
    }
}
