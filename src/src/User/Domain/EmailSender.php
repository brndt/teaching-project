<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\Token;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;

interface EmailSender
{
    public function sendEmailConfirmation(Email $email, Uuid $userId, string $firstName, string $lastName, Token $confirmationToken);
    public function sendPasswordReset(Email $email, Uuid $userId, string $firstName, string $lastName, Token $confirmationToken);
}