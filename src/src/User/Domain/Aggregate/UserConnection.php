<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Aggregate;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\ValueObject\RequestStatus;

final class UserConnection
{
    private Uuid $userId;
    private Uuid $friendId;
    private RequestStatus $status;

    public function __construct(Uuid $userId, Uuid $friendId, RequestStatus $status)
    {
        $this->userId = $userId;
        $this->friendId = $friendId;
        $this->status = $status;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getFriendId(): Uuid
    {
        return $this->friendId;
    }

    public function getStatus(): RequestStatus
    {
        return $this->status;
    }
}