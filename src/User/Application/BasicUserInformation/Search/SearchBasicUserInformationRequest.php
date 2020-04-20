<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\BasicUserInformation\Search;

final class SearchBasicUserInformationRequest
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