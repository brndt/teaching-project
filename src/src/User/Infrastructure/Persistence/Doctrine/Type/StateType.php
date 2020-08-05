<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use LaSalle\StudentTeacher\User\Domain\ValueObject\State\StateFactory;

class StateType extends Type
{
    const STATE = 'state';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return StateFactory::fromStateName($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return (string) $value;
    }

    public function getName()
    {
        return self::STATE;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'VARCHAR';
    }
}