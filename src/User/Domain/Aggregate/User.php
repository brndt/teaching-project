<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Aggregate;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEvent;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Event\UserCreatedDomainEvent;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;

final class User
{
    private Uuid $id;
    private Email $email;
    private Password $password;
    private string $firstName;
    private string $lastName;
    private Roles $roles;
    private \DateTimeImmutable $created;
    private ?string $image;
    private ?string $experience;
    private ?string $education;
    private array $eventStream;

    public function __construct(
        Uuid $id,
        Email $email,
        Password $password,
        string $firstName,
        string $lastName,
        Roles $roles,
        DateTimeImmutable $created,
        ?string $image = null,
        ?string $experience = null,
        ?string $education = null
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->roles = $roles;
        $this->created = $created;
        $this->image = $image;
        $this->experience = $experience;
        $this->education = $education;
    }

    public static function create(
        Uuid $id,
        Email $email,
        Password $password,
        string $firstName,
        string $lastName,
        Roles $roles,
        DateTimeImmutable $created,
        ?string $image = null,
        ?string $experience = null,
        ?string $education = null
    ): self {
        $instance = new static(
            $id,
            $email,
            $password,
            $firstName,
            $lastName,
            $roles,
            $created,
            $image,
            $experience,
            $education
        );

        $instance->recordThat(
            new UserCreatedDomainEvent(
                $instance->getId(),
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

    public function setEmail(Email $email): void
    {
        $this->email = $email;
    }

    public function setPassword(Password $password): void
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

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getPassword()
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