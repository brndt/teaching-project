<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class UpdateUserInformationRequest
{
    private string $id;
    private string $email;
    private string $firstName;
    private string $lastName;
    private string $image;
    private string $experience;
    private string $education;
    private string $requestAuthorId;

    public function __construct(
        string $requestAuthorId,
        string $id,
        string $email,
        string $firstName,
        string $lastName,
        string $image,
        string $experience,
        string $education
    ) {
        $this->requestAuthorId = $requestAuthorId;
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
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

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }

}