<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure;

use LaSalle\StudentTeacher\Shared\Domain\DomainEvent;
use LaSalle\StudentTeacher\Shared\Domain\DomainEventBus;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class EventDispatcherDomainEventBus implements DomainEventBus
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatch(DomainEvent $event, string $eventName = null)
    {
        $this->eventDispatcher->dispatch($event);
    }
}