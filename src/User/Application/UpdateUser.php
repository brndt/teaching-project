<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application;

use LaSalle\StudentTeacher\User\Domain\Roles;
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
        $roles = Roles::fromPrimitives($request->getRoles());

        $user = new User();
        $user->setEmail($request->getEmail());
        $user->setPassword($request->getPassword());
        $user->setFirstName($request->getFirstName());
        $user->setLastName($request->getLastName());
        $user->setRoles($roles);
        $user->setId($request->getId());
        $user->setImage($request->getImage());
        $user->setEducation($request->getEducation());
        $user->setExperience($request->getExperience());
        $user->setCreated($request->getCreated());

        $this->repository->update($user);
        return new UserResponse(
            $user->getEmail(),
            $user->getPassword(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getRoles()->toPrimitives(),
            $user->getId(),
            $request->getImage(),
            $request->getEducation(),
            $request->getExperience(),
            $request->getCreated()
        );
    }
}