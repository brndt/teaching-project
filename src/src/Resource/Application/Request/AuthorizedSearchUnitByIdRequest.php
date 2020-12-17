<?php
declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;


final class AuthorizedSearchUnitByIdRequest
{
    private string $requestAuthorId;
    private string $courseId;
    private string $unitId;

    public function __construct(string $requestAuthorId, string $courseId, string $unitId)
    {
        $this->requestAuthorId = $requestAuthorId;
        $this->courseId = $courseId;
        $this->unitId = $unitId;
    }

    public function getCourseId(): string
    {
        return $this->courseId;
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }

    public function getUnitId(): string
    {
        return $this->unitId;
    }

}
