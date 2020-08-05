<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\Event\DomainEventBus;
use LaSalle\StudentTeacher\Shared\Domain\RandomStringGenerator;
use LaSalle\StudentTeacher\User\Application\Request\SendEmailConfirmationRequest;
use LaSalle\StudentTeacher\User\Domain\EmailSender;
use LaSalle\StudentTeacher\User\Domain\Event\EmailConfirmationRequestReceivedDomainEvent;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

final class SendEmailConfirmationService extends UserService
{
    private RandomStringGenerator $randomStringGenerator;
    private DomainEventBus $eventBus;

    public function __construct(DomainEventBus $eventBus, RandomStringGenerator $randomStringGenerator, UserRepository $userRepository)
    {
        parent::__construct($userRepository);
        $this->randomStringGenerator = $randomStringGenerator;
        $this->eventBus = $eventBus;
    }

    public function __invoke(SendEmailConfirmationRequest $request): void
    {
        $email = $this->createEmailFromPrimitive($request->getEmail());
        $user = $this->userRepository->ofEmail($email);
        $this->ensureUserExists($user);

        $token = new Token($this->randomStringGenerator->generate());

        $user->setConfirmationToken($token);
        $user->setExpirationDate(new \DateTimeImmutable('+1 day'));

        $this->userRepository->save($user);

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
