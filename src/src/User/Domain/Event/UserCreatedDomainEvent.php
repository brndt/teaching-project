<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Event;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEvent;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;

final class UserCreatedDomainEvent extends DomainEvent
{
    private string $email;
    private string $firstName;
    private string $lastName;
    private string $confirmationToken;

    public function __construct(
        string $aggregateId,
        string $email,
        string $firstName,
        string $lastName,
        string $confirmationToken
    ) {
        parent::__construct($aggregateId);
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->confirmationToken = $confirmationToken;
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
        return 'user.created';
    }

    public function getConfirmationToken(): string
    {
        return $this->confirmationToken;
    }
}
