<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Domain;

interface EmailSender
{
    public function sendEmailConfirmation(
        string $email,
        string $userId,
        string $firstName,
        string $lastName,
        string $confirmationToken
    );

    public function sendPasswordReset(
        string $email,
        string $userId,
        string $firstName,
        string $lastName,
        string $confirmationToken
    );
}
