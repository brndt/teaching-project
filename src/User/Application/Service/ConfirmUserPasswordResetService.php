<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Request\ConfirmUserPasswordResetRequest;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

final class ConfirmUserPasswordResetService
{
    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
        $this->userService = new UserService($userRepository);
    }

    public function __invoke(ConfirmUserPasswordResetRequest $request): void
    {
        $userId = new Uuid($request->getUserId());
        $user = $this->userService->findUser($userId);

        $confirmationToken = new Token($request->getConfirmationToken());
        $user->validateConfirmationToken($confirmationToken);

        $newPassword = Password::fromPlainPassword($request->getNewPassword());

        $user->setConfirmationToken(null);
        $user->setExpirationDate(null);
        $user->setPassword($newPassword);

        $this->repository->save($user);
    }
}
