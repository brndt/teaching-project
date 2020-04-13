<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Validator;

use LaSalle\StudentTeacher\User\Application\User\Search\SearchUserByEmail;
use LaSalle\StudentTeacher\User\Application\User\Search\SearchUserByEmailRequest;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class UniqueEmailValidator extends ConstraintValidator
{
    private SearchUserByEmail $searchUser;

    public function __construct(SearchUserByEmail $searchUser)
    {
        $this->searchUser = $searchUser;
    }

    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        $existingEmail = ($this->searchUser)(new SearchUserByEmailRequest($value));

        if (!$existingEmail) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
