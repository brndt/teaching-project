<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\User\Search;

final class SearchUserByUuidRequest
{
    private string $uuid;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }
}