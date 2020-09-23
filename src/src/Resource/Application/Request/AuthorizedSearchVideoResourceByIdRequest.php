<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class AuthorizedSearchVideoResourceByIdRequest
{
    private string $requestAuthorId;
    private string $resourceId;

    public function __construct(string $requestAuthorId, string $resourceId)
    {
        $this->requestAuthorId = $requestAuthorId;
        $this->resourceId = $resourceId;
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
