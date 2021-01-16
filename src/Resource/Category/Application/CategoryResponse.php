<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Category\Application;

final class CategoryResponse
{
    public function __construct(private string $id, private string $name, private string $status)
    {
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