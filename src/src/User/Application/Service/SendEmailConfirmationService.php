<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\User\Application\Request\SendEmailConfirmationRequest;
use LaSalle\StudentTeacher\User\Domain\EmailSender;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

final class SendEmailConfirmationService extends UserService
{
    private EmailSender $emailSender;

    public function __construct(EmailSender $emailSender, UserRepository $userRepository)
    {
        parent::__construct($userRepository);
        $this->emailSender = $emailSender;
    }

    public function __invoke(SendEmailConfirmationRequest $request): void
    {
        $email = $this->createEmailFromPrimitive($request->getEmail());
        $user = $this->userRepository->ofEmail($email);
        $this->ensureUserExists($user);

        $user->setConfirmationToken(Token::generate());
        $user->setExpirationDate(new \DateTimeImmutable('+1 day'));

        $this->userRepository->save($user);

        $this->emailSender->sendEmailConfirmation(
            $user->getEmail(),
            $user->getId(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getConfirmationToken()
        );
    }
}