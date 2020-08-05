<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Event;

use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEvent;

final class EmailConfirmationRequestReceivedDomainEvent extends DomainEvent
{
    private string $email;
    private string $firstName;
    private string $lastName;
    private string $confirmationToken;

    public function __construct(
        string $aggregateId,
        string $email,
        string $firstName,
        string $lastName,
        string $confirmationToken,
        string $eventId = null,
        string $occurredOn = null
    ) {
        parent::__construct($aggregateId, $eventId, $occurredOn);
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->confirmationToken = $confirmationToken;
    }

    public function getConfirmationToken(): string
    {
        return $this->confirmationToken;
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

    public static function eventName(): string
    {
        return 'email.confirmation.request.received';
    }

    public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): DomainEvent {
        return new self($aggregateId, $body['email'], $body['firstName'], $body['lastName'], $body['confirmationToken'], $eventId, $occurredOn);
    }

    public function toPrimitives(): array
    {
        return [
            'email' => $this->getEmail(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'confirmationToken' => $this->getConfirmationToken(),
        ];
    }
}
