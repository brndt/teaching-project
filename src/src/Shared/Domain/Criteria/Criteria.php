<?php

declare(strict_types = 1);

namespace LaSalle\StudentTeacher\Shared\Domain\Criteria;

final class Criteria
{
    private Filters $filters;
    private Order   $order;
    private Operator $operator;
    private ?int    $offset;
    private ?int    $limit;

    public function __construct(Filters $filters, Order $order, ?Operator $operator, ?int $offset, ?int $limit)
    {
        $this->filters = $filters;
        $this->order   = $order;
        $this->offset  = $offset;
        $this->limit   = $limit;
        $this->operator = $operator ?? Operator::fromValue(Operator::AND);
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
