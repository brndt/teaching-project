<?php


namespace LaSalle\StudentTeacher\Resource\Application\Response;


use DateTimeImmutable;

class ResourceResponse
{
    private string $id;
    private string $unitId;
    private string $name;
    private ?string $description;
    private string $content;
    private DateTimeImmutable $created;
    private ?DateTimeImmutable $modified;
    private string $status;


    public function __construct(
        string $id,
        string $unitId,
        string $name,
        ?string $description,
        string $content,
        DateTimeImmutable $created,
        ?DateTimeImmutable $modified,
        string $status
    )
    {
        $this->id = $id;
        $this->unitId = $unitId;
        $this->name = $name;
        $this->description = $description;
        $this->content = $content;
        $this->created = $created;
        $this->modified = $modified;
        $this->status = $status;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUnitId(): string
    {
        return $this->unitId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    public function getModified(): ?DateTimeImmutable
    {
        return $this->modified;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getContent(): string
    {
        return $this->content;
    }


}
