<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\User\Application\Request\SignInRequest;
use LaSalle\StudentTeacher\User\Application\Response\UserResponse;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;

final class SignInService extends UserService
{
    public function __invoke(SignInRequest $request): UserResponse
    {
        $email = $this->createEmailFromPrimitive($request->getEmail());

        $user = $this->userRepository->ofEmail($email);
        $this->ensureUserExists($user);

        $this->ensureUserEnabled($user);
        $this->verifyPassword($request->getPassword(), $user->getPassword());

        return $this->buildResponse($user);
    }

    private function buildResponse(User $user): UserResponse
    {
        return new UserResponse(
            $user->getId()->toString(),
            $user->getFirstName()->toString(),
            $user->getLastName()->toString(),
            $user->getRoles()->getArrayOfPrimitives(),
            $user->getCreated()->format('Y-m-d H:i:s'),
            $user->getImage(),
            $user->getExperience(),
            $user->getEducation(),
        );
    }
}