<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Event;

use AMQPException;
use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEvent;
use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEventBus;
use function Lambdish\Phunctional\each;

final class RabbitMqEventBus implements DomainEventBus
{
    private RabbitMqConnection    $connection;
    private string                $exchangeName;

    public function __construct(
        RabbitMqConnection $connection,
        string $exchangeName
    ) {
        $this->connection = $connection;
        $this->exchangeName = $exchangeName;
    }

    public function dispatch(DomainEvent ...$events): void
    {
        each($this->publisher(), $events);
    }

    private function publisher(): callable
    {
        return function (DomainEvent $event) {
            try {
                $this->publishEvent($event);
            } catch (AMQPException $error) {
                throw new \InvalidArgumentException($error->getMessage());
            }
        };
    }

    private function publishEvent(DomainEvent $event): void
    {
        $body = DomainEventJsonSerializer::serialize($event);
        $routingKey = $event::eventName();
        $messageId = $event->getId();

        $hola = $this->connection->exchange($this->exchangeName)->publish(
            $body,
            $routingKey,
            AMQP_NOPARAM,
            [
                'message_id' => $messageId,
                'content_type' => 'application/json',
                'content_encoding' => 'utf-8',
            ]
        );
        var_dump($hola);
    }
}
