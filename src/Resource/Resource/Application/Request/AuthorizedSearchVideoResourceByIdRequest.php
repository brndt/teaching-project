<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Resource\Application\Request;

final class AuthorizedSearchVideoResourceByIdRequest
{
    public function __construct(private string $requestAuthorId, private string $resourceId)
    {
    }

    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }
}
