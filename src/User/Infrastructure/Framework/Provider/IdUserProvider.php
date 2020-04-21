<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Provider;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\User\Search\SearchUserById;
use LaSalle\StudentTeacher\User\Application\User\Search\SearchUserByIdRequest;
use LaSalle\StudentTeacher\User\Domain\Email;
use LaSalle\StudentTeacher\User\Domain\Password;
use LaSalle\StudentTeacher\User\Domain\Roles;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\Entity\SymfonyUser;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class IdUserProvider implements UserProviderInterface
{
    private SearchUserById $searchUser;

    public function __construct(SearchUserById $searchUser)
    {
        $this->searchUser = $searchUser;
    }

    public function loadUserByUsername($username)
    {
        try {
            $searchUserResponse = ($this->searchUser)(new SearchUserByIdRequest($username));
        } catch (UserNotFoundException $e) {
            throw new UsernameNotFoundException('No user found for id ' . $username);
        }

        return new SymfonyUser(
            Uuid::fromString($searchUserResponse->getId()),
            new Email($searchUserResponse->getEmail()),
            Password::fromHashedPassword($searchUserResponse->getPassword()),
            $searchUserResponse->getFirstName(),
            $searchUserResponse->getLastName(),
            Roles::fromArrayOfPrimitives($searchUserResponse->getRoles()),
            new \DateTimeImmutable($searchUserResponse->getCreated()),
            $searchUserResponse->getImage(),
            $searchUserResponse->getExperience(),
            $searchUserResponse->getEducation()
        );
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof SymfonyUser) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }
        return $this->loadUserByUsername($user->getId());
    }


    public function supportsClass($class)
    {
        return SymfonyUser::class === $class;
    }
}