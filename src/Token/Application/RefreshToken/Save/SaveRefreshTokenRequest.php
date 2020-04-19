<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\RefreshToken\Save;

final class SaveRefreshTokenRequest
{
    private string $uuid;
    private \DateTime $valid;

    public function __construct(string $uuid, \DateTime $valid)
    {
        $this->uuid = $uuid;
        $this->valid = $valid;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getValid(): \DateTime
    {
        return $this->valid;
    }

}