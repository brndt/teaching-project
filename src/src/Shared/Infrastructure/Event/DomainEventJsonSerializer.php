<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Event;

use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEvent;

final class DomainEventJsonSerializer
{
    public static function serialize(DomainEvent $domainEvent): string
    {
        return json_encode(
            [
                'data' => [
                    'id'          => $domainEvent->getId(),
                    'type'        => $domainEvent::eventName(),
                    'occurred_on' => $domainEvent->getOccurredOn(),
                    'attributes'  => array_merge($domainEvent->toPrimitives(), ['id' => $domainEvent->getAggregateId()]),
                ],
                'meta' => [],
            ]
        );
    }
}

