<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Request\ConfirmUserEmailRequest;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

final class ConfirmUserEmailService
{
    private UserService $userService;

    public function __construct(private UserRepository $repository)
    {
        $this->userService = new UserService($this->repository);
    }

    public function __invoke(ConfirmUserEmailRequest $request): void
    {
        $userId = new Uuid($request->getUserId());
        $user = $this->userService->findUser($userId);

        $confirmationToken = new Token($request->getConfirmationToken());
        $user->validateConfirmationToken($confirmationToken);

        $user->setConfirmationToken(null);
        $user->setExpirationDate(null);
        $user->setEnabled(true);

        $this->repository->save($user);
    }
}
