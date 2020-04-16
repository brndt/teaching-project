<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\DomainEvent;
use LaSalle\StudentTeacher\User\Domain\Event\UserCreatedDomainEvent;

class User
{
    protected string $email;
    protected string $uuid;
    protected string $password;
    protected string $firstName;
    protected string $lastName;
    protected Roles $roles;
    protected \DateTimeImmutable $created;
    protected ?int $id;
    protected ?string $image;
    protected ?string $experience;
    protected ?string $education;
    private array $eventStream;

    public function __construct(
        string $uuid,
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        Roles $roles,
        DateTimeImmutable $created,
        ?int $id = null,
        ?string $image = null,
        ?string $experience = null,
        ?string $education = null
    ) {
        $this->uuid = $uuid;
        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->roles = $roles;
        $this->created = $created;
        $this->id = $id;
        $this->image = $image;
        $this->experience = $experience;
        $this->education = $education;
    }

    public static function create(
        string $uuid,
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        Roles $roles,
        DateTimeImmutable $created,
        ?int $id = null,
        ?string $image = null,
        ?string $experience = null,
        ?string $education = null
    ): self {
        $instance = new static($uuid, $email, $password, $firstName, $lastName, $roles, $created, $id, $image, $experience, $education);

        $instance->recordThat(
            new UserCreatedDomainEvent(
                $instance->getUuid(),
                $instance->getEmail(),
                $instance->getFirstName(),
                $instance->getLastName(),
                $instance->getCreated()
            )
        );

        return $instance;
    }

    public function pullDomainEvents(): array
    {
        $events = $this->eventStream ?: [];
        $this->eventStream = [];

        return $events;
    }

    private function recordThat(DomainEvent $event): void
    {
        $this->eventStream[] = $event;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function setRoles(Roles $roles): void
    {
        $this->roles = $roles;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function setExperience(?string $experience): void
    {
        $this->experience = $experience;
    }

    public function setCreated(\DateTimeImmutable $created): void
    {
        $this->created = $created;
    }

    public function setEducation(?string $education): void
    {
        $this->education = $education;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function getCreated(): ?\DateTimeImmutable
    {
        return $this->created;
    }

    public function getEducation(): ?string
    {
        return $this->education;
    }
}