<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Request;

final class AuthorizedSearchCategoriesByCriteriaRequest
{
    public function __construct(
        private string $requestAuthorId,
        private array $filters,
        private ?string $orderBy,
        private ?string $order,
        private ?string $operator,
        private ?int $offset,
        private ?int $limit
    ) {
    }

    public function getFilters(): array
    {
        return $this->filters;
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

    public function getRequestAuthorId(): string
    {
        return $this->requestAuthorId;
    }
}
