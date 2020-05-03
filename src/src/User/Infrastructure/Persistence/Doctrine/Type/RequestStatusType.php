<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use LaSalle\StudentTeacher\User\Domain\ValueObject\RequestStatus;

class RequestStatusType extends Type
{
    const NAME = 'requestStatus';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new RequestStatus($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function getName()
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'VARCHAR';
    }
}