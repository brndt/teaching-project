<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Command;

use LaSalle\StudentTeacher\Shared\Infrastructure\Event\DomainEventSubscriberLocator;
use LaSalle\StudentTeacher\Shared\Infrastructure\Event\RabbitMqDomainEventsConsumer;
use LaSalle\StudentTeacher\Shared\Infrastructure\Persistence\Doctrine\DatabaseConnections;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Lambdish\Phunctional\repeat;

final class ConsumeRabbitMqDomainEventsCommand extends Command
{
    protected static                     $defaultName = 'domain-events:rabbitmq:consume';
    private RabbitMqDomainEventsConsumer $consumer;
    private DomainEventSubscriberLocator $locator;

    public function __construct(
        RabbitMqDomainEventsConsumer $consumer,
        DomainEventSubscriberLocator $locator
    ) {
        parent::__construct();

        $this->consumer    = $consumer;
        $this->locator     = $locator;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Consume domain events from the RabbitMQ')
            ->addArgument('queue', InputArgument::REQUIRED, 'Queue name')
            ->addArgument('quantity', InputArgument::REQUIRED, 'Quantity of events to process');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $queueName       = (string) $input->getArgument('queue');
        $eventsToProcess = (int) $input->getArgument('quantity');

        repeat($this->consumer($queueName), $eventsToProcess);
    }

    private function consumer(string $queueName): callable
    {
        return function () use ($queueName) {
            $subscriber = $this->locator->withRabbitMqQueueNamed($queueName);

            $this->consumer->consume($subscriber, $queueName);

            //$this->connections->clear();
        };
    }
}
