<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain;

use LaSalle\StudentTeacher\User\Domain\ValueObject\ConfirmationToken;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;

interface EmailSender
{
    public function sendEmailConfirmation(Email $email, string $firstName, string $lastName, ConfirmationToken $confirmationToken);
}