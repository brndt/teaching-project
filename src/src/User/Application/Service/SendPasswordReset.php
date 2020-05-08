<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Token\Domain\ValueObject\Token;
use LaSalle\StudentTeacher\User\Application\Request\SendPasswordResetRequest;
use LaSalle\StudentTeacher\User\Domain\EmailSender;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;

final class SendPasswordReset extends UserService
{
    private EmailSender $emailSender;

    public function __construct(EmailSender $emailSender, UserRepository $userRepository)
    {
        parent::__construct($userRepository);
        $this->emailSender = $emailSender;
    }

    public function __invoke(SendPasswordResetRequest $request): void
    {
        $user = $this->userRepository->ofEmail($this->createEmailFromPrimitive($request->getEmail()));

        $this->ensureUserExists($user);
        $this->ensureUserEnabled($user);

        $user->setConfirmationToken(Token::generate());

        $this->userRepository->save($user);

        $this->emailSender->sendPasswordReset(
            $user->getEmail(),
            $user->getId(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getConfirmationToken()
        );
    }
}