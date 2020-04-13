<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application;

final class UpdateUserRequest
{
    protected string $email;
    protected string $password;
    protected string $firstName;
    protected string $lastName;
    protected array $roles;
    protected ?int $id;
    protected ?string $image;
    protected ?string $experience;
    protected ?\DateTimeImmutable $created;
    protected ?string $education;

    public function __construct(
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        array $roles,
        int $id = null,
        string $image = null,
        string $education = null,
        string $experience = null,
        \DateTimeImmutable $created = null
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->roles = $roles;
        $this->id = $id;
        $this->image = $image;
        $this->education = $education;
        $this->experience = $experience;
        $this->created = $created;
    }

    public function getEmail(): string
    {
        return $this->email;
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

    public function getRoles(): array
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