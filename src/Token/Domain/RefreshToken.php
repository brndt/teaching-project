<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Domain;

final class RefreshToken
{
    private ?int $id;
    private string $refreshToken;
    private string $username;
    private \DateTime $valid;

    public function __construct(string $username, \DateTime $valid, int $id = null)
    {
        $this->id = $id;
        $this->setRefreshToken();
        $this->username = $username;
        $this->valid = $valid;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getValid(): \DateTime
    {
        return $this->valid;
    }

    private function setRefreshToken()
    {
        $this->refreshToken = bin2hex(openssl_random_pseudo_bytes(64));
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function setValid(\DateTime $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

}