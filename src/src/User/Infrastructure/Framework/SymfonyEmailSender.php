<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework;

use LaSalle\StudentTeacher\Token\Domain\ValueObject\Token;
use LaSalle\StudentTeacher\User\Domain\EmailSender;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
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

    public function sendEmailConfirmation(Email $email, string $firstName, string $lastName, Token $confirmationToken): void
    {
        $email = (new TemplatedEmail())
            ->from('log.visualization@gmail.com')
            ->to($email->toString())
            ->subject('Please confirm the email')
            ->htmlTemplate('email.html.twig')
            ->context(
                [
                    'name' => $firstName.' '.$lastName,
                    'url_confirmation' => $_SERVER['APP_URL'].'?token='.$confirmationToken->toString(),
                ]
            );
        $this->mailer->send($email);
    }
}