<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Domain;

final class Token
{
    private ?int $id;
    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

}