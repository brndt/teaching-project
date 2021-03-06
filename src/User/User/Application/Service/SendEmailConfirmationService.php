<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Application\Service;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEventBus;
use LaSalle\StudentTeacher\Shared\Domain\RandomStringGenerator;
use LaSalle\StudentTeacher\User\User\Application\Request\SendEmailConfirmationRequest;
use LaSalle\StudentTeacher\User\User\Domain\Event\EmailConfirmationRequestReceivedDomainEvent;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\User\Domain\Service\UserService;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Token;

final class SendEmailConfirmationService
{
    public function __construct(
        private DomainEventBus $eventBus,
        private RandomStringGenerator $randomStringGenerator,
        UserRepository $userRepository
    ) {
        $this->repository = $userRepository;
        $this->userService = new UserService($userRepository);
    }

    public function __invoke(SendEmailConfirmationRequest $request): void
    {
        $email = new Email($request->getEmail());
        $user = $this->userService->findUserByEmail($email);

        $token = new Token($this->randomStringGenerator->generate());

        $user->setConfirmationToken($token);
        $user->setExpirationDate(new DateTimeImmutable('+1 day'));

        $this->repository->save($user);

        $this->eventBus->dispatch(
            new EmailConfirmationRequestReceivedDomainEvent(
                $user->getId()->toString(),
                $user->getEmail()->toString(),
                $user->getFirstName()->toString(),
                $user->getLastName()->toString(),
                $user->getConfirmationToken()->toString()
            )
        );
    }
}
