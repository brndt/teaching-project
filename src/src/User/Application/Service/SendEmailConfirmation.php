<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\Token;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyEnabledException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\SendEmailConfirmationRequest;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\EmailSender;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidEmailException;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;

final class SendEmailConfirmation
{
    private EmailSender $emailSender;
    private UserRepository $userRepository;

    public function __construct(EmailSender $emailSender, UserRepository $userRepository)
    {
        $this->emailSender = $emailSender;
        $this->userRepository = $userRepository;
    }

    public function __invoke(SendEmailConfirmationRequest $request): void
    {
        $user = $this->userRepository->ofEmail($this->createEmailFromPrimitive($request->getEmail()));

        $this->checkIfExists($user);
        $this->checkIfEnabled($user);

        $user->setConfirmationToken(Token::generate());

        $this->userRepository->save($user);

        $this->emailSender->sendEmailConfirmation(
            $user->getEmail(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getConfirmationToken()
        );
    }

    private function createEmailFromPrimitive(string $email): Email
    {
        try {
            return new Email($email);
        } catch (InvalidEmailException $exception) {
            throw new InvalidArgumentValidationException($exception->getMessage());
        }
    }

    private function checkIfExists(?User $user): void
    {
        if (null === $user) {
            throw new UserNotFoundException();
        }
    }

    private function checkIfEnabled(User $user): void
    {
        if (true === $user->getEnabled()) {
            throw new UserAlreadyEnabledException();
        }
    }
}