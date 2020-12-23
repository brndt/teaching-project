<?php

declare(strict_types = 1);

namespace LaSalle\StudentTeacher\Shared\Domain\Criteria;

final class Criteria
{
    private Operator $operator;

    public function __construct(
        private Filters $filters,
        private Order $order,
        ?Operator $operator,
        private ?int $offset,
        private ?int $limit
    ) {
        $this->operator = $operator ?? Operator::fromValue(Operator:: AND);
    }

    public function hasFilters(): bool
    {
        return $this->filters->count() > 0;
    }

    public function hasOrder(): bool
    {
        return !$this->order->isNone();
    }

    public function plainFilters(): array
    {
        return $this->filters->filters();
    }

    public function filters(): Filters
    {
        return $this->filters;
    }

    public function order(): Order
    {
        return $this->order;
    }

    public function operator(): Operator
    {
        return $this->operator;
    }

    public function offset(): ?int
    {
        return $this->offset;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    public function serialize(): string
    {
        return sprintf(
            '%s~~%s~~%s~~%s',
            $this->filters->serialize(),
            $this->order->serialize(),
            $this->offset,
            $this->limit
        );
    }
}
