<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\User\Application\Exception\IncorrectPasswordException;
use LaSalle\StudentTeacher\User\Application\Request\SignInRequest;
use LaSalle\StudentTeacher\User\Application\Response\UserResponse;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;

final class SignInService extends UserService
{
    public function __invoke(SignInRequest $request): UserResponse
    {
        $user = $this->userRepository->ofEmail($this->createEmailFromPrimitive($request->getEmail()));

        $this->ensureUserExists($user);
        $this->ensureUserEnabled($user);
        $this->verifyPassword($request->getPassword(), $user->getPassword());

        return $this->buildResponse($user);
    }

    private function verifyPassword(string $plainPassword, Password $userPassword): void
    {
        if (false === Password::verify($plainPassword, $userPassword)) {
            throw new IncorrectPasswordException();
        }
    }

    private function buildResponse(User $user): UserResponse
    {
        return new UserResponse(
            $user->getId()->toString(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getRoles()->getArrayOfPrimitives(),
            $user->getCreated()->format('Y-m-d H:i:s'),
            $user->getImage(),
            $user->getExperience(),
            $user->getEducation(),
        );
    }
}