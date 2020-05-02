<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\Aggregate;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\ValueObject\RequestStatus;

final class StudentTeacherConnection
{
    private Uuid $studentId;
    private Uuid $teacherId;
    private RequestStatus $status;

    public function __construct(Uuid $studentId, Uuid $teacherId, RequestStatus $status)
    {
        $this->studentId = $studentId;
        $this->teacherId = $teacherId;
        $this->status = $status;
    }

    public function getStudentId(): Uuid
    {
        return $this->studentId;
    }

    public function getTeacherId(): Uuid
    {
        return $this->teacherId;
    }

    public function getStatus(): RequestStatus
    {
        return $this->status;
    }
}