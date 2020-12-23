<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Response;

use DateTimeImmutable;

final class UserResponse
{
    public function __construct(
        private string $id,
        private string $firstName,
        private string $lastName,
        private array $roles,
        private DateTimeImmutable $created,
        private ?string $image,
        private ?string $experience,
        private ?string $education
    ) {
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
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

    public function toPrimitives(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'roles' => $this->roles,
            'created' => $this->created,
            'image' => $this->image,
            'education' => $this->education,
            'experience' => $this->experience
        ];
    }

}
