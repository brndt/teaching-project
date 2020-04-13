<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use LaSalle\StudentTeacher\User\Domain\Roles;
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
            ->setValue('roles', ':roles')
            ->setParameter('email', $user->getEmail(), 'string')
            ->setParameter('password', $user->getPassword(), 'string')
            ->setParameter('first_name', $user->getFirstName(), 'string')
            ->setParameter('last_name', $user->getLastName(), 'string')
            ->setParameter('roles', $user->getRoles(), 'roles')
            ->execute();
    }

    public function update(User $user): void
    {
        $this->connection->createQueryBuilder()
            ->update('user_account')
            ->set('email', ':email')
            ->set('password', ':password')
            ->set('first_name', ':first_name')
            ->set('last_name', ':last_name')
            ->set('image', ':image')
            ->set('roles', ':roles')
            ->set('education', ':education')
            ->set('experience', ':experience')
            ->where('id = :id')
            ->setParameter('email', $user->getEmail(), 'string')
            ->setParameter('password', $user->getPassword(), 'string')
            ->setParameter('first_name', $user->getFirstName(), 'string')
            ->setParameter('last_name', $user->getLastName(), 'string')
            ->setParameter('education', $user->getEducation(), 'string')
            ->setParameter('experience', $user->getExperience(), 'string')
            ->setParameter('roles', $user->getRoles(), 'roles')
            ->setParameter('image', $user->getImage(), 'string')
            ->setParameter('id', $user->getId(), 'integer')
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

        $user = new User();
        $user->setEmail($userAsArray['email']);
        $user->setPassword($userAsArray['password']);
        $user->setFirstName($userAsArray['first_name']);
        $user->setLastName($userAsArray['last_name']);
        $user->setRoles(Roles::fromPrimitives(json_decode($userAsArray['roles'])));
        $user->setId($userAsArray['id']);
        $user->setImage($userAsArray['image']);
        $user->setEducation($userAsArray['education']);
        $user->setExperience($userAsArray['experience']);
        $user->setCreated(new \DateTimeImmutable($userAsArray['created']));

        return $user;
    }
}