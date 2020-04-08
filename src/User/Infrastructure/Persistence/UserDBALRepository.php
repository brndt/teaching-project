<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use LaSalle\StudentTeacher\User\Domain\User;
use LaSalle\StudentTeacher\User\Domain\UserRepository;

final class UserDBALRepository implements UserRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save(User $user): void
    {
        $this->connection->createQueryBuilder()
            ->insert('user_account')
            ->setValue('email', ':email')
            ->setValue('password', ':password')
            ->setValue('first_name', ':first_name')
            ->setValue('last_name', ':last_name')
            ->setValue('created', 'now()')
            ->setValue('role', ':role')
            ->setParameter('email', $user->getEmail(), 'string')
            ->setParameter('password', $user->getPassword(), 'string')
            ->setParameter('first_name', $user->getFirstName(), 'string')
            ->setParameter('last_name', $user->getLastName(), 'string')
            ->setParameter('role', $user->getRole(), 'string')
            ->execute();
    }

    public function searchByEmail(string $email): ?User
    {
        $searchUserQuery = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('user_account')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->execute();
        $userAsArray = $searchUserQuery->fetch();

        if (!$userAsArray) {
            return null;
        }

        return new User(
            $userAsArray['email'],
            $userAsArray['password'],
            $userAsArray['first_name'],
            $userAsArray['last_name'],
            $userAsArray['role'],
            $userAsArray['id'],
            $userAsArray['image'],
            $userAsArray['education'],
            $userAsArray['experience'],
            new \DateTimeImmutable($userAsArray['created'])
        );
    }
}