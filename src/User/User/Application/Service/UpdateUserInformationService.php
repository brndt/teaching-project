<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\User\Application\Request\UpdateUserInformationRequest;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\User\Domain\Service\UserService;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Name;

final class UpdateUserInformationService
{
    private UserService $userService;

    public function __construct(private UserRepository $repository, private AuthorizationService $authorizationService)
    {
        $this->userService = new UserService($repository);
    }

    public function __invoke(UpdateUserInformationRequest $request): void
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $userId = new Uuid($request->getUserId());
        $email = new Email($request->getEmail());
        $firstName = new Name($request->getFirstName());
        $lastName = new Name($request->getLastName());

        $requestAuthor = $this->userService->findUser($requestAuthorId);
        $userToUpdate = $this->userService->findUser($userId);

        $this->authorizationService->ensureRequestAuthorIsCertainUser($requestAuthor, $userToUpdate);
        $this->userService->ensureNewEmailIsAvailable($email, $userToUpdate->getEmail());

        $userToUpdate->setEmail($email);
        $userToUpdate->setFirstName($firstName);
        $userToUpdate->setLastName($lastName);
        $userToUpdate->setEducation($request->getEducation());
        $userToUpdate->setExperience($request->getExperience());

        $this->repository->save($userToUpdate);
    }
}
