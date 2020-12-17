<?php
declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;


final class AuthorizedSearchResourcesByCriteriaRequest
{
    private string $requestAuthorId;
    private ?string $courseId;
    private ?string $unitId;
    private ?string $orderBy;
    private ?string $order;
    private ?string $operator;
    private ?int $offset;
    private ?int $limit;

    public function __construct(
        string $requestAuthorId,
        ?string $courseId,
        ?string $unitId,
        ?string $orderBy,
        ?string $order,
        ?string $operator,
        ?int $offset,
        ?int $limit
    ) {
        $this->requestAuthorId = $requestAuthorId;
        $this->courseId = $courseId;
        $this->unitId = $unitId;
        $this->orderBy = $orderBy;
        $this->order = $order;
        $this->operator = $operator;
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }

    public function getCourseId(): ?string
    {
        return $this->courseId;
    }

    public function getUnitId(): ?string
    {
        return $this->unitId;
    }

    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    public function getOrder(): ?string
    {
        return $this->order;
    }

    public function getOperator(): ?string
    {
        return $this->operator;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

}
