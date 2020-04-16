<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\User;

final class UserResponse
{
    private string $uuid;
    private string $email;
    private string $password;
    private string $firstName;
    private string $lastName;
    private array $roles;
    private string $created;
    private ?int $id;
    private ?string $image;
    private ?string $education;
    private ?string $experience;


    public function __construct(
        string $uuid,
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        array $roles,
        string $created,
        ?int $id,
        ?string $image,
        ?string $experience,
        ?string $education
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

    public function getUuid(): string
    {
        return $this->uuid;
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

    public function getCreated(): string
    {
        return $this->created;
    }

    public function getId(): ?int
    {
        return $this->id;
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

}