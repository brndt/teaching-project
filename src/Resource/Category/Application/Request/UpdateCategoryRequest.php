<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Category\Application\Request;

final class UpdateCategoryRequest
{
    public function __construct(
        private string $requestAuthorId,
        private string $categoryId,
        private string $newName,
        private string $newStatus
    ) {
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

    public function getNewStatus(): string
    {
        return $this->newStatus;
    }
}