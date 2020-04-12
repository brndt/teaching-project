<?php

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class UniqueEmail extends Constraint
{
    public string $message = 'This email is already registered';
}
