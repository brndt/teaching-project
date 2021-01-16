<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Category\Domain;

use LaSalle\StudentTeacher\Resource\Category\Domain\Aggregate\Category;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class CategoryBuilder
{
    private Uuid $id;
    private string $name;
    private Status $status;

    public function __construct()
    {
        $this->id = Uuid::generate();
        $this->name = 'random-category-name';
        $this->status = new Status('published');
    }

    public function withId(Uuid $id): CategoryBuilder
    {
        $this->id = $id;
        return $this;
    }

    public function withName(string $name): CategoryBuilder
    {
        $this->name = $name;
        return $this;
    }

    public function withStatus(Status $status): CategoryBuilder
    {
        $this->status = $status;
        return $this;
    }

    public function build(): Category
    {
        return new Category($this->id, $this->name, $this->status);
    }
}