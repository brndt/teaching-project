<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\TestQuestion;

use function Lambdish\Phunctional\map;

final class TestQuestionsType extends JsonType
{
    const NAME = 'test_questions';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }
        return map($this->questionFromValues(), parent::convertToPHPValue($value, $platform));
    }

    private function questionFromValues(): callable
    {
        return static function (array $values): TestQuestion {
            return TestQuestion::fromValues($values);
        };
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return parent::convertToDatabaseValue(map($this->questionToValues(), $value), $platform);
    }

    private function questionToValues(): callable
    {
        return static function (TestQuestion $question): array {
            return $question->toValues();
        };
    }

    public function getName()
    {
        return self::NAME;
    }
}
