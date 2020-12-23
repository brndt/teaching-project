<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Response;

final class UserConnectionResponse
{
    public function __construct(
        private string $userId,
        private string $friendId,
        private ?string $status = null,
        private ?string $specifierId = null
    ) {
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getFriendId(): ?string
    {
        return $this->friendId;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getSpecifierId(): ?string
    {
        return $this->specifierId;
    }
}