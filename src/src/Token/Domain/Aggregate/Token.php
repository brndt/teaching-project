<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Domain\Aggregate;

final class Token
{
    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}