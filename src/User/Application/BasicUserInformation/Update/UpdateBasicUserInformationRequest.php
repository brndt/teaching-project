<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\BasicUserInformation\Update;

final class UpdateBasicUserInformationRequest
{
    private int $id;
    private string $email;
    private string $firstName;
    private string $lastName;
    private string $image;
    private string $education;
    private string $experience;

    public function __construct(
        int $id,
        string $email,
        string $firstName,
        string $lastName,
        string $image,
        string $education,
        string $experience
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->image = $image;
        $this->education = $education;
        $this->experience = $experience;
    }

    public function getId(): ?int
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