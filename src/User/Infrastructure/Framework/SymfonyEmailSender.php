<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework;

use LaSalle\StudentTeacher\User\Domain\EmailSender;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

final class SymfonyEmailSender implements EmailSender
{
    public function __construct(private MailerInterface $mailer, private Environment $twig)
    {
    }

    public function sendEmailConfirmation(
        string $email,
        string $userId,
        string $firstName,
        string $lastName,
        string $confirmationToken
    ): void {
        $email = (new TemplatedEmail())
            ->from('log.visualization@gmail.com')
            ->to($email)
            ->subject('Please confirm the email')
            ->htmlTemplate('email_confirmation.html.twig')
            ->context(
                [
                    'name' => $firstName . ' ' . $lastName,
                    'url_confirmation' => $_SERVER['APP_FRONTEND'] . 'users/' . $userId . '/confirm-email/?token=' . $confirmationToken,
                ]
            );
        $this->mailer->send($email);
    }

    public function sendPasswordReset(
        string $email,
        string $userId,
        string $firstName,
        string $lastName,
        string $confirmationToken
    ): void {
        $email = (new TemplatedEmail())
            ->from('log.visualization@gmail.com')
            ->to($email)
            ->subject('Password reset request')
            ->htmlTemplate('password_reset.html.twig')
            ->context(
                [
                    'name' => $firstName . ' ' . $lastName,
                    'url_confirmation' => $_SERVER['APP_FRONTEND'] . 'users/' . $userId . '/reset-password/?token=' . $confirmationToken,
                ]
            );
        $this->mailer->send($email);
    }
}
