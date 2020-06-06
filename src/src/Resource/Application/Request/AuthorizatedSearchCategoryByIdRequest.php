<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class AuthorizatedSearchCategoryByIdRequest
{
    private string $requestAuthorId;
    private string $categoryId;

    public function __construct(string $requestAuthorId, string $categoryName)
    {
        $this->requestAuthorId = $requestAuthorId;
        $this->categoryId = $categoryName;
    }

    public function getCategoryId(): string
    {
        return $this->categoryId;
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }
}
