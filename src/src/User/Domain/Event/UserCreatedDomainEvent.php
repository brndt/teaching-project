<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Event;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEvent;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;

final class UserCreatedDomainEvent implements DomainEvent
{
    private Uuid $id;
    private Uuid $aggregateUuid;
    private Email $email;
    private string $firstName;
    private string $lastName;
    private DateTimeImmutable $occurredOn;

    public function __construct(
        Uuid $id,
        Uuid $aggregateUuid,
        Email $email,
        string $firstName,
        string $lastName,
        DateTimeImmutable $occurredOn
    ) {
        $this->id = $id;
        $this->aggregateUuid = $aggregateUuid;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->occurredOn = $occurredOn;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getOccurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public function getAggregateUuid(): Uuid
    {
        return $this->aggregateUuid;
    }

    public function getEmail(): Email
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
}