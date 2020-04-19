<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Infrastructure\Persistence;

use LaSalle\StudentTeacher\User\Domain\User;
use LaSalle\StudentTeacher\User\Domain\UserRepository;

final class InMemoryUserRepository implements UserRepository
{

    private array $users = [];

    public function save(User $user): void
    {
        $this->users[$user->getId()] = $user;
    }

    public function updateBasicInformation(User $user): void
    {
        $this->users[$user->getId()] = $user;
    }

    public function updatePassword(User $user): void
    {
        $this->users[$user->getId()] = $user;
    }

    public function searchByEmail(string $email): ?User
    {
        $userToFind = null;
        foreach ($this->users as $user) {
            if ($email == $user->getEmail()) {
                $userToFind = $user;
            }
        }
        return $userToFind;
    }

    public function searchByUuid(string $uuid): ?User
    {
        $userToFind = null;
        foreach ($this->users as $user) {
            if ($user == $user->getUuid()) {
                $userToFind = $user;
            }
        }
        return $userToFind;
    }

    public function searchById(int $id): ?User
    {
        return $this->users[$id];
    }
}