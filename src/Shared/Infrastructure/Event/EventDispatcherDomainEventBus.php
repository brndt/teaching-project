<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Event;

use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEvent;
use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEventBus;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class EventDispatcherDomainEventBus implements DomainEventBus
{
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function dispatch(DomainEvent $event, string $eventName = null)
    {
        $this->eventDispatcher->dispatch($event);
    }
}