<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Unit\Domain;

use LaSalle\StudentTeacher\Resource\Unit\Domain\Aggregate\Unit;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class UnitBuilder
{
    private Uuid $id;
    private Uuid $courseId;
    private string $name;
    private ?string $description;
    private string $level;
    private \DateTimeImmutable $created;
    private ?\DateTimeImmutable $modified;
    private Status $status;

    public function __construct()
    {
        $this->id = Uuid::generate();
        $this->courseId = Uuid::generate();
        $this->name = 'some_name';
        $this->description = 'some description';
        $this->level = 'some_level';
        $this->created = new \DateTimeImmutable();
        $this->modified = new \DateTimeImmutable();
        $this->status = new Status('published');
    }

    public function withId(Uuid $id): UnitBuilder
    {
        $this->id = $id;
        return $this;
    }

    public function withCourseId(Uuid $courseId): UnitBuilder
    {
        $this->courseId = $courseId;
        return $this;
    }

    public function withName(string $name): UnitBuilder
    {
        $this->name = $name;
        return $this;
    }

    public function withDescription(?string $description): UnitBuilder
    {
        $this->description = $description;
        return $this;
    }

    public function withLevel(string $level): UnitBuilder
    {
        $this->level = $level;
        return $this;
    }

    public function withCreated(\DateTimeImmutable $created): UnitBuilder
    {
        $this->created = $created;
        return $this;
    }

    public function withModified(?\DateTimeImmutable $modified): UnitBuilder
    {
        $this->modified = $modified;
        return $this;
    }

    public function withStatus(Status $status): UnitBuilder
    {
        $this->status = $status;
        return $this;
    }

    public function build(): Unit
    {
        return new Unit(
            $this->id,
            $this->courseId,
            $this->name,
            $this->description,
            $this->level,
            $this->created,
            $this->modified,
            $this->status
        );
    }
}
