<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Category\Domain\Aggregate;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class Category
{
    public function __construct(private Uuid $id, private string $name, private Status $status)
    {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }
}