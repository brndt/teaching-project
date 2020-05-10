<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\EmailSender;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

final class SymfonyEmailSender implements EmailSender
{
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendEmailConfirmation(Email $email, Uuid $userId, string $firstName, string $lastName, Token $confirmationToken): void
    {
        $email = (new TemplatedEmail())
            ->from('log.visualization@gmail.com')
            ->to($email->toString())
            ->subject('Please confirm the email')
            ->htmlTemplate('email_confirmation.html.twig')
            ->context(
                [
                    'name' => $firstName.' '.$lastName,
                    'url_confirmation' => $_SERVER['APP_URL'].'/users/'.$userId->toString().'/email_confirmation/?token='.$confirmationToken->toString(),
                ]
            );
        $this->mailer->send($email);
    }

    public function sendPasswordReset(Email $email, Uuid $userId, string $firstName, string $lastName, Token $confirmationToken): void
    {
        $email = (new TemplatedEmail())
            ->from('log.visualization@gmail.com')
            ->to($email->toString())
            ->subject('Password reset request')
            ->htmlTemplate('password_reset.html.twig')
            ->context(
                [
                    'name' => $firstName.' '.$lastName,
                    'url_confirmation' => $_SERVER['APP_URL'].'/users/'.$userId->toString().'/password_reset/?token='.$confirmationToken->toString(),
                ]
            );
        $this->mailer->send($email);
    }
}