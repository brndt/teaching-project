<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Event;

use LaSalle\StudentTeacher\User\Domain\Event\UserCreatedDomainEvent;

final class SendEmailOnUserCreated
{
    public function __invoke(UserCreatedDomainEvent $createdDomainEvent)
    {
    }
}