<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Domain\Event;

use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEvent;

final class UserCreatedDomainEvent extends DomainEvent
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

    public static function eventName(): string
    {
        return 'user.created';
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

    public function getConfirmationToken(): string
    {
        return $this->confirmationToken;
    }
}
