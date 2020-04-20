<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\BasicUserInformation;

final class BasicUserInformationResponse
{
    private string $id;
    private string $email;
    private string $firstName;
    private string $lastName;
    private array $roles;
    private ?string $image;
    private ?string $experience;
    private ?string $education;

    public function __construct(
        string $id,
        string $email,
        string $firstName,
        string $lastName,
        array $roles,
        ?string $image = null,
        ?string $education = null,
        ?string $experience = null
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->roles = $roles;
        $this->image = $image;
        $this->experience = $experience;
        $this->education = $education;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
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

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function getEducation(): ?string
    {
        return $this->education;
    }

}