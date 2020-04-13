<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\User;

final class UserResponse
{
    private int $id;
    private string $email;
    private string $password;
    private string $firstName;
    private string $lastName;
    private array $roles;
    private ?string $image;
    private ?string $education;
    private ?string $experience;
    private string $created;
    private string $uuid;

    public function __construct(
        int $id,
        string $uuid,
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        array $roles,
        ?string $image,
        ?string $education,
        ?string $experience,
        string $created
    ) {
        $this->id = $id;
        $this->uuid = $uuid;
        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->roles = $roles;
        $this->image = $image;
        $this->education = $education;
        $this->experience = $experience;
        $this->created = $created;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getEducation(): ?string
    {
        return $this->education;
    }

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function getCreated(): ?string
    {
        return $this->created;
    }

}