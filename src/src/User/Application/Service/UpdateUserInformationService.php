<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserInformationRequest;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;

final class UpdateUserInformationService
{
    private UserRepository $repository;
    private UserService $userService;
    private AuthorizationService $authorizationService;

    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
        $this->userService = new UserService($userRepository);
        $this->authorizationService = new AuthorizationService();
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
