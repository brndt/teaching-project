<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Category\Application;

final class AuthorizedSearchCategoryByIdRequest
{
    public function __construct(private string $requestAuthorId, private string $categoryId)
    {
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
