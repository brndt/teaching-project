<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

interface EmailSender
{
    public function sendEmailConfirmation(string $email, string $userId, string $firstName, string $lastName, string $confirmationToken);
    public function sendPasswordReset(string $email, string $userId, string $firstName, string $lastName, string $confirmationToken);
}
