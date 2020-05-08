<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class CreateCategoryRequest
{
    private string $requestAuthorId;
    private string $categoryName;

    public function __construct(string $requestAuthorId, string $categoryName)
    {
        $this->requestAuthorId = $requestAuthorId;
        $this->categoryName = $categoryName;
    }

    public function getCategoryName(): string
    {
        return $this->categoryName;
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }
}