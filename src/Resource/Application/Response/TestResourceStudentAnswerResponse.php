<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Response;

use DateTimeImmutable;

final class TestResourceStudentAnswerResponse
{
    private string $id;
    private string $resourceId;
    private string $studentId;
    private ?string $points;
    private ?string $teacher_comment;
    private DateTimeImmutable $created;
    private ?DateTimeImmutable $modified;
    private ?DateTimeImmutable $until;
    private string $status;
    private array $assumptions;

    public function __construct(
        string $id,
        string $resourceId,
        string $studentId,
        ?string $points,
        ?string $teacher_comment,
        DateTimeImmutable $created,
        ?DateTimeImmutable $modified,
        ?DateTimeImmutable $until,
        string $status,
        array $assumptions
    ) {
        $this->id = $id;
        $this->resourceId = $resourceId;
        $this->studentId = $studentId;
        $this->points = $points;
        $this->teacher_comment = $teacher_comment;
        $this->created = $created;
        $this->modified = $modified;
        $this->until = $until;
        $this->status = $status;
        $this->assumptions = $assumptions;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    public function getStudentId(): string
    {
        return $this->studentId;
    }

    public function getPoints(): ?string
    {
        return $this->points;
    }

    public function getTeacherComment(): ?string
    {
        return $this->teacher_comment;
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    public function getModified(): ?DateTimeImmutable
    {
        return $this->modified;
    }

    public function getUntil(): ?DateTimeImmutable
    {
        return $this->until;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getAssumptions(): array
    {
        return $this->assumptions;
    }
}
