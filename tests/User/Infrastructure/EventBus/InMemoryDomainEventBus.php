<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Infrastructure\EventBus;

use LaSalle\StudentTeacher\Shared\Domain\DomainEvent;
use LaSalle\StudentTeacher\Shared\Domain\DomainEventBus;
use LaSalle\StudentTeacher\Shared\Domain\DomainEventSubscriber;

final class InMemoryDomainEventBus implements DomainEventBus
{

    private array $subscribers;

    public function __construct()
    {
        $this->subscribers = [];
    }

    public function subscribe(DomainEventSubscriber $domainEventSubscriber)
    {
        $this->subscribers[] = $domainEventSubscriber;
    }

    public function dispatch(DomainEvent $event, string $eventName = null)
    {
        foreach ($this->subscribers as $aSubscriber) {
            $aSubscriber->handle($event);
        }
    }
}