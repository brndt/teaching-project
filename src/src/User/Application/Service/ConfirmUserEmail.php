<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\InvalidConfirmationTokenException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\ConfirmUserEmailRequest;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;

final class ConfirmUserEmail
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(ConfirmUserEmailRequest $request)
    {
        try {
            $userId = new Uuid($request->getUserId());
        } catch (InvalidUuidException $error) {
            throw new InvalidArgumentValidationException($error->getMessage());
        }

        $user = $this->userRepository->ofId($userId);

        if (null === $user) {
            throw new UserNotFoundException();
        }

        if ($request->getConfirmationToken() !== $user->getConfirmationToken()->toString()) {
            throw new InvalidConfirmationTokenException();
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $this->userRepository->save($user);
    }
}