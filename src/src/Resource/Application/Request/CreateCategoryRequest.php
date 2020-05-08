<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class CreateCategoryRequest
{
    private string $requestAuthorId;
    private string $name;

    public function __construct(string $requestAuthorId, string $name)
    {
        $this->requestAuthorId = $requestAuthorId;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }
}