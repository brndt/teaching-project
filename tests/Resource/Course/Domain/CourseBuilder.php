<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Course\Domain;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Resource\Course\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class CourseBuilder
{
    private Uuid $id;
    private Uuid $teacherId;
    private Uuid $categoryId;
    private string $name;
    private ?string $description;
    private string $level;
    private \DateTimeImmutable $created;
    private ?\DateTimeImmutable $modified;
    private Status $status;

    public function __construct()
    {
        $this->id = Uuid::generate();
        $this->teacherId = Uuid::generate();
        $this->categoryId = Uuid::generate();
        $this->name = 'some_name';
        $this->description = 'some_description';
        $this->level = 'some_level';
        $this->created = new DateTimeImmutable();
        $this->modified = new DateTimeImmutable();
        $this->status = new Status('published');
    }

    public function withId(Uuid $id): CourseBuilder
    {
        $this->id = $id;
        return $this;
    }

    public function withTeacherId(Uuid $teacherId): CourseBuilder
    {
        $this->teacherId = $teacherId;
        return $this;
    }

    public function withCategoryId(Uuid $categoryId): CourseBuilder
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function withName(string $name): CourseBuilder
    {
        $this->name = $name;
        return $this;
    }

    public function withDescription(?string $description): CourseBuilder
    {
        $this->description = $description;
        return $this;
    }

    public function withLevel(string $level): CourseBuilder
    {
        $this->level = $level;
        return $this;
    }

    public function withCreated(DateTimeImmutable $created): CourseBuilder
    {
        $this->created = $created;
        return $this;
    }

    public function withModified(?DateTimeImmutable $modified): CourseBuilder
    {
        $this->modified = $modified;
        return $this;
    }

    public function withStatus(Status $status): CourseBuilder
    {
        $this->status = $status;
        return $this;
    }

    public function build(): Course
    {
        return new Course(
            $this->id,
            $this->teacherId,
            $this->categoryId,
            $this->name,
            $this->description,
            $this->level,
            $this->created,
            $this->modified,
            $this->status
        );
    }
}