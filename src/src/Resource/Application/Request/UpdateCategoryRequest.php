<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class UpdateCategoryRequest
{
    private string $requestAuthorId;
    private string $categoryId;
    private string $newName;

    public function __construct(string $requestAuthorId, string $categoryId, string $newName)
    {
        $this->requestAuthorId = $requestAuthorId;
        $this->categoryId = $categoryId;
        $this->newName = $newName;
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }

    public function getCategoryId(): string
    {
        return $this->categoryId;
    }

    public function getNewName(): string
    {
        return $this->newName;
    }
}