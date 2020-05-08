<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;

final class RolesType extends Type
{
    const NAME = 'roles';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return Roles::fromArrayOfPrimitives(explode(",", $value));
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (false == is_string($value)) {
            return implode(",", ($value->getArrayOfPrimitives()));
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