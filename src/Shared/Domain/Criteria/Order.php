<?php

declare(strict_types = 1);

namespace LaSalle\StudentTeacher\Shared\Domain\Criteria;

final class Order
{
    public function __construct(private OrderBy $orderBy, private OrderType $orderType)
    {
    }

    public static function createDesc(OrderBy $orderBy): Order
    {
        return new self($orderBy, OrderType::desc());
    }

    public function orderBy(): OrderBy
    {
        return $this->orderBy;
    }

    public function orderType(): OrderType
    {
        return $this->orderType;
    }

    public static function fromValues(?string $orderBy, ?string $order): Order
    {
        return null === $orderBy ? self::none() : new Order(new OrderBy($orderBy), new OrderType($order));
    }

    public function isNone(): bool
    {
        return $this->orderType()->isNone();
    }

    public static function none(): Order
    {
        return new Order(new OrderBy(''), OrderType::none());
    }

    public function serialize(): string
    {
        return sprintf('%s.%s', $this->orderBy->value(), $this->orderType->value());
    }
}
