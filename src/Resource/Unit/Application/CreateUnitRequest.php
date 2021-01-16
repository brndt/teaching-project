<?php


namespace LaSalle\StudentTeacher\Resource\Unit\Application;


use DateTimeImmutable;

class CreateUnitRequest
{
    public function __construct(
        private string $requestAuthorId,
        private string $courseId,
        private string $name,
        private ?string $description,
        private string $level,
        private DateTimeImmutable $created,
        private ?DateTimeImmutable $modified,
        private string $status
    ) {
    }

    /**
     * @return string
     */
    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }

    /**
     * @return string
     */
    public function getCourseId(): string
    {
        return $this->courseId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getLevel(): string
    {
        return $this->level;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getModified(): ?DateTimeImmutable
    {
        return $this->modified;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

}
