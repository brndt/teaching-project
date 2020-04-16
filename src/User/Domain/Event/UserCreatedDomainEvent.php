<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Event;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\DomainEvent;
use Ramsey\Uuid\Uuid;

final class UserCreatedDomainEvent implements DomainEvent
{
    private string $id;
    private string $aggregateUuid;
    private string $email;
    private string $firstName;
    private string $lastName;
    private DateTimeImmutable $occurredOn;

    public function __construct(
        string $aggregateUuid,
        string $email,
        string $firstName,
        string $lastName,
        DateTimeImmutable $occurredOn
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->aggregateUuid = $aggregateUuid;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->occurredOn = $occurredOn;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getOccurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public function getAggregateUuid(): string
    {
        return $this->aggregateUuid;
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
}