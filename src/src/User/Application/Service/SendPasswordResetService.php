<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\RandomStringGenerator;
use LaSalle\StudentTeacher\User\Application\Request\SendPasswordResetRequest;
use LaSalle\StudentTeacher\User\Domain\EmailSender;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

final class SendPasswordResetService extends UserService
{
    private EmailSender $emailSender;
    private RandomStringGenerator $randomStringGenerator;

    public function __construct(EmailSender $emailSender, RandomStringGenerator $randomStringGenerator, UserRepository $userRepository)
    {
        parent::__construct($userRepository);
        $this->emailSender = $emailSender;
        $this->randomStringGenerator = $randomStringGenerator;
    }

    public function __invoke(SendPasswordResetRequest $request): void
    {
        $email = $this->createEmailFromPrimitive($request->getEmail());
        $user = $this->userRepository->ofEmail($email);
        $this->ensureUserExists($user);
        $this->ensureUserEnabled($user);

        $token = new Token($this->randomStringGenerator->generate());

        $user->setConfirmationToken($token);
        $user->setExpirationDate(new \DateTimeImmutable('+1 day'));

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