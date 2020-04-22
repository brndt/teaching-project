<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\RefreshTokenString;

final class RefreshTokenStringType extends Type
{
    const NAME = 'refreshTokenString';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return RefreshTokenString::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->toString();
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