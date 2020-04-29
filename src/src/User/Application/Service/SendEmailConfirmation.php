<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserAlreadyEnabledException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\EmailSender;
use LaSalle\StudentTeacher\User\Domain\Exception\InvalidEmailException;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\ConfirmationToken;
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
        try {
            $email = new Email($request->getEmail());
        } catch (InvalidEmailException $exception) {
            throw new InvalidArgumentValidationException($exception->getMessage());
        }

        $user = $this->userRepository->ofEmail($email);

        if (null === $user) {
            throw new UserNotFoundException();
        }

        if (true === $user->getEnabled()) {
            throw new UserAlreadyEnabledException();
        }

        $user->setConfirmationToken(ConfirmationToken::generate());

        $this->userRepository->save($user);

        $this->emailSender->sendEmailConfirmation($user->getEmail(), $user->getFirstName(), $user->getLastName(), $user->getConfirmationToken());
    }
}