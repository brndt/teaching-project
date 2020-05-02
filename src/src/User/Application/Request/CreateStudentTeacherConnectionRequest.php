<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Request;

final class CreateStudentTeacherConnectionRequest
{
    private string $requestingId;
    private string $pendingId;

    public function __construct(string $requestingId, string $pendingId)
    {
        $this->requestingId = $requestingId;
        $this->pendingId = $pendingId;
    }

    public function getRequestingId(): string
    {
        return $this->requestingId;
    }

    public function getPendingId(): string
    {
        return $this->pendingId;
    }
}