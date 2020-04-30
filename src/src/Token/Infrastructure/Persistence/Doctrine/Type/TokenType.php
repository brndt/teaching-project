<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\Token;

final class TokenType extends Type
{
    const NAME = 'token';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null !== $value) {
            return new Token($value);
        }
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null !== $value) {
            return $value->toString();
        }
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