<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application;

use LaSalle\StudentTeacher\User\Domain\User;
use LaSalle\StudentTeacher\User\Domain\UserRepository;

final class UpdateUser
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UpdateUserRequest $request): UserResponse
    {
        $user = new User($request->getEmail(), $request->getPassword(), $request->getFirstName(), $request->getLastName(), $request->getRole(), $request->getId(), $request->getImage(), $request->getEducation(), $request->getExperience(), $request->getCreated());
        $this->repository->update($user);
        return new UserResponse($user->getEmail(), $user->getPassword(), $user->getFirstName(), $user->getLastName(), $user->getRole(), $user->getId(), $request->getImage(), $request->getEducation(), $request->getExperience(), $request->getCreated());
    }
}