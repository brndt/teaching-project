<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Domain\Event;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

abstract class DomainEvent
{
    protected string $id;
    private string $occurredOn;

    public function __construct(private string $aggregateId, string $eventId = null, string $occurredOn = null)
    {
        $this->id = Uuid::generate()->toString();
        $this->occurredOn = (new DateTimeImmutable())->format('Y-m-d H:i:s');
    }

    abstract public static function eventName(): string;


    public function getAggregateId(): string
    {
        return $this->aggregateId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getOccurredOn(): string
    {
        return $this->occurredOn;
    }
}
