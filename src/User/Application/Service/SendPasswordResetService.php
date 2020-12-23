<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEventBus;
use LaSalle\StudentTeacher\Shared\Domain\RandomStringGenerator;
use LaSalle\StudentTeacher\User\Application\Request\SendPasswordResetRequest;
use LaSalle\StudentTeacher\User\Domain\Event\PasswordResetRequestReceivedDomainEvent;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

final class SendPasswordResetService
{
    public function __construct(
        private RandomStringGenerator $randomStringGenerator,
        private DomainEventBus $eventBus,
        UserRepository $repository
    ) {
        $this->repository = $repository;
        $this->userService = new UserService($repository);
    }

    public function __invoke(SendPasswordResetRequest $request): void
    {
        $email = new Email($request->getEmail());
        $user = $this->userService->findUserByEmail($email);
        $user->ensureUserEnabled();

        $token = new Token($this->randomStringGenerator->generate());

        $user->setConfirmationToken($token);
        $user->setExpirationDate(new \DateTimeImmutable('+1 day'));

        $this->repository->save($user);

        $this->eventBus->dispatch(
            new PasswordResetRequestReceivedDomainEvent(
                $user->getId()->toString(),
                $user->getEmail()->toString(),
                $user->getFirstName()->toString(),
                $user->getLastName()->toString(),
                $user->getConfirmationToken()->toString()
            )
        );
    }

}
