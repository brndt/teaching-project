<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Response;

final class CategoryResponse
{
    private string $id;
    private string $name;
    private string $status;

    public function __construct(string $id, string $name, string $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->status = $status;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}