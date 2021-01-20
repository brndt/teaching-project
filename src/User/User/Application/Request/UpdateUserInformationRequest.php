<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Application\Request;

final class UpdateUserInformationRequest
{
    public function __construct(
        private string $requestAuthorId,
        private string $userId,
        private string $email,
        private string $firstName,
        private string $lastName,
        private ?string $experience,
        private ?string $education
    ) {
    }

    public function getUserId(): string
    {
        return $this->userId;
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
