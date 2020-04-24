<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Infrastructure\Framework\Event;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

final class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $payload['id'] = $event->getUser()->getId();

        $event->setData($payload);
    }
}