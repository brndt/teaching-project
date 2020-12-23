<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class CreateCategoryRequest
{
    public function __construct(private string $requestAuthorId, private string $categoryName, private string $categoryStatus)
    {
    }

    public function getCategoryName(): string
    {
        return $this->categoryName;
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }

    public function getCategoryStatus(): string
    {
        return $this->categoryStatus;
    }
}