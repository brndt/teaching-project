<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

interface EmailSender
{
    public function sendEmailConfirmation(Email $email, Uuid $userId, Name $firstName, Name $lastName, Token $confirmationToken);
    public function sendPasswordReset(Email $email, Uuid $userId, Name $firstName, Name $lastName, Token $confirmationToken);
}