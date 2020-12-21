<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Event;

use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEvent;
use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEventBus;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerDomainEventBus implements DomainEventBus
{
    private MessageBusInterface $eventBus;

    public function __construct(MessageBusInterface $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function dispatch(DomainEvent $event, string $eventName = null)
    {
        $this->eventBus->dispatch($event);
    }
}

