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
        return Roles::fromArrayOfPrimitives(json_decode($value));
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return json_encode($value->toArrayOfPrimitives());
    }

    public function getName()
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'JSON';
    }
}