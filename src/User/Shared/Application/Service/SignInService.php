<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Shared\Application\Service;

use LaSalle\StudentTeacher\User\Shared\Application\Request\SignInRequest;
use LaSalle\StudentTeacher\User\Shared\Application\Response\UserResponse;
use LaSalle\StudentTeacher\User\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\User\Domain\Service\UserService;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Email;

final class SignInService
{
    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
        $this->userService = new UserService($userRepository);
    }

    public function __invoke(SignInRequest $request): UserResponse
    {
        $email = new Email($request->getEmail());
        $user = $this->userService->findUserByEmail($email);
        $user->ensureUserEnabled();

        $user->getPassword()->verify($request->getPassword());

        return $this->buildResponse($user);
    }

    private function buildResponse(User $user): UserResponse
    {
        return new UserResponse(
            $user->getId()->toString(),
            $user->getFirstName()->toString(),
            $user->getLastName()->toString(),
            $user->getRoles()->getArrayOfPrimitives(),
            $user->getCreated(),
            $user->getImage(),
            $user->getExperience(),
            $user->getEducation(),
        );
    }
}
