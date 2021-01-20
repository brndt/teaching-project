<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Password;

final class PasswordType extends Type
{
    const NAME = 'password';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return Password::fromHashedPassword($value);
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