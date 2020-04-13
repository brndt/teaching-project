<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\User\Search;

final class SearchUserByIdRequest
{
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}