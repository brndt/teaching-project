<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Provider;

use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\User\Search\SearchUserByUuid;
use LaSalle\StudentTeacher\User\Application\User\Search\SearchUserByUuidRequest;
use LaSalle\StudentTeacher\User\Domain\Roles;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\Entity\SymfonyUser;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class UuidUserProvider implements UserProviderInterface
{
    private SearchUserByUuid $searchUser;

    public function __construct(SearchUserByUuid $searchUser)
    {
        $this->searchUser = $searchUser;
    }

    public function loadUserByUsername($uuid)
    {
        try {
            $searchUserResponse = ($this->searchUser)(new SearchUserByUuidRequest($uuid));
        } catch (UserNotFoundException $e) {
            throw new UsernameNotFoundException('No user found for uuid ' . $uuid);
        }

        return new SymfonyUser(
            $searchUserResponse->getUuid(),
            $searchUserResponse->getEmail(),
            $searchUserResponse->getPassword(),
            $searchUserResponse->getFirstName(),
            $searchUserResponse->getLastName(),
            Roles::fromPrimitives($searchUserResponse->getRoles()),
            new \DateTimeImmutable($searchUserResponse->getCreated()),
            $searchUserResponse->getId(),
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
        return $this->loadUserByUsername($user->getEmail());
    }


    public function supportsClass($class)
    {
        return SymfonyUser::class === $class;
    }
}