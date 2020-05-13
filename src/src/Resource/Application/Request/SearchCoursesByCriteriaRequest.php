<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class SearchCoursesByCriteriaRequest
{
    private string $requestAuthorId;
    private ?string $userId;
    private ?string $orderBy;
    private ?string $order;
    private ?string $operator;
    private ?int $offset;
    private ?int $limit;

    public function __construct(
        string $requestAuthorId,
        ?string $userId,
        ?string $orderBy,
        ?string $order,
        ?string $operator,
        ?int $offset,
        ?int $limit
    ) {
        $this->requestAuthorId = $requestAuthorId;
        $this->userId = $userId;
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

    public function getUserId(): ?string
    {
        return $this->userId;
    }
}