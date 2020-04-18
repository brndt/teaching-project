<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Token\Create;

final class CreateTokenRequest
{
    private string $uuid;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }
}