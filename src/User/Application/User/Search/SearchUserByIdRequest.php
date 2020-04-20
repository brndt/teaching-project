<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\User\Search;

final class SearchUserByIdRequest
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}