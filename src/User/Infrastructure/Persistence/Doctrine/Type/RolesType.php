<?php

declare(strict_types=1);

namespace LaSalle\GroupOne\Logging\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use LaSalle\StudentTeacher\User\Domain\Roles;

final class RolesType extends Type
{
    const LEVEL = 'roles';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return Roles::fromPrimitives(json_decode($value));
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return json_encode($value->toPrimitives());
    }

    public function getName()
    {
        return self::LEVEL;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'JSON';
    }
}