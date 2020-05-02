<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain\ValueObject;

final class RequestStatus
{
    private string $requestStatus;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_WITHDRAWN = 'withdrawn';

    public function __construct(string $requestStatus)
    {
        $this->setValue($requestStatus);
    }

    public function toString(): string
    {
        return $this->requestStatus;
    }

    public function __toString()
    {
        return $this->requestStatus;
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

    private function setValue(string $requestStatus)
    {
        $this->assertValueInArray($requestStatus);
        $this->requestStatus = $requestStatus;
    }

    private function assertValueInArray(string $requestStatus): void
    {
        if (false === in_array($requestStatus, $this->ArrayOfRequestStatus())) {
            throw new \InvalidArgumentException();
        }
    }
}