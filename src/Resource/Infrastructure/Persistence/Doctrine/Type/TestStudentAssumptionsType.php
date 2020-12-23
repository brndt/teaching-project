<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\StudentTestAnswer;

use function Lambdish\Phunctional\map;

final class TestStudentAssumptionsType extends JsonType
{
    const NAME = 'test_student_assumptions';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }
        return map($this->assumptionFromValues(), parent::convertToPHPValue($value, $platform));
    }

    private function assumptionFromValues(): callable
    {
        return static function (array $values): StudentTestAnswer {
            return StudentTestAnswer::fromValues($values);
        };
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return parent::convertToDatabaseValue(map($this->assumptionToValues(), $value), $platform);
    }

    private function assumptionToValues(): callable
    {
        return static function (StudentTestAnswer $assumption): array {
            return $assumption->toValues();
        };
    }

    public function getName()
    {
        return self::NAME;
    }
}
