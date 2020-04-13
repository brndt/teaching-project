<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain;

class User
{
    protected string $email;
    protected string $uuid;
    protected string $password;
    protected string $firstName;
    protected string $lastName;
    protected Roles $roles;
    protected ?int $id;
    protected ?string $image;
    protected ?string $experience;
    protected ?\DateTimeImmutable $created;
    protected ?string $education;

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

    public function setCreated(?\DateTimeImmutable $created): void
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