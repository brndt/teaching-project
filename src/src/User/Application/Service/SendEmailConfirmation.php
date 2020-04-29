<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyEnabledException;
use LaSalle\StudentTeacher\User\Domain\EmailSender;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\ConfirmationToken;

final class SendEmailConfirmation
{
    private EmailSender $emailSender;
    private UserRepository $userRepository;

    public function __construct(EmailSender $emailSender, UserRepository $userRepository)
    {
        $this->emailSender = $emailSender;
        $this->userRepository = $userRepository;
    }

    public function __invoke(Uuid $id): void
    {
        $user = $this->userRepository->ofId($id);

        if (true === $user->getEnabled()) {
            throw new UserAlreadyEnabledException();
        }

        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken(ConfirmationToken::generate());
        }

        $this->emailSender->sendEmailConfirmation($user->getEmail(), $user->getFirstName(), $user->getLastName(), $user->getConfirmationToken());
    }
}