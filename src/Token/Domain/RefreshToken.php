<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Domain;

final class RefreshToken
{
    private ?int $id;
    private string $refreshToken;
    private string $uuid;
    private \DateTime $valid;

    public function __construct(string $uuid, string $refreshToken, \DateTime $valid, int $id = null)
    {
        $this->id = $id;
        $this->refreshToken = $refreshToken;
        $this->uuid = $uuid;
        $this->valid = $valid;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getValid(): \DateTime
    {
        return $this->valid;
    }

    private function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function setValid(\DateTime $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function isValid()
    {
        return $this->valid >= new \DateTime();
    }

}