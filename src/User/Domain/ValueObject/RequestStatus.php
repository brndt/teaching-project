<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject;

use InvalidArgumentException;
use Stringable;

final class RequestStatus implements Stringable
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_WITHDRAWN = 'withdrawn';
    private string $requestStatus;

    public function __construct(string $requestStatus)
    {
        $this->setValue($requestStatus);
    }

    private function setValue(string $requestStatus)
    {
        $this->assertValueInArray($requestStatus);
        $this->requestStatus = $requestStatus;
    }

    private function assertValueInArray(string $requestStatus): void
    {
        if (false === in_array($requestStatus, $this->ArrayOfRequestStatus())) {
            throw new InvalidArgumentException();
        }
    }

    public static function ArrayOfRequestStatus(): array
    {
        return [
            self::STATUS_APPROVED,
            self::STATUS_PENDING,
            self::STATUS_REJECTED,
            self::STATUS_WITHDRAWN,
        ];
    }

    public function toString(): string
    {
        return $this->requestStatus;
    }

    public function __toString(): string
    {
        return $this->requestStatus;
    }
}