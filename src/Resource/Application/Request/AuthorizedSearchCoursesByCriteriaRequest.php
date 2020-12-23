<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class AuthorizedSearchCoursesByCriteriaRequest
{
    public function __construct(private string $requestAuthorId, private ?string $teacherId, private ?string $orderBy, private ?string $order, private ?string $operator, private ?int $offset, private ?int $limit)
    {
    }

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
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

    public function getTeacherId(): ?string
    {
        return $this->teacherId;
    }
}
