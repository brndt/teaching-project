<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Provider;

use LaSalle\StudentTeacher\User\Application\SearchUserByEmail;
use LaSalle\StudentTeacher\User\Application\SearchUserByEmailRequest;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\User\SymfonyUser;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class CustomUserProvider implements UserProviderInterface
{
    private SearchUserByEmail $searchUser;

    public function __construct(SearchUserByEmail $searchUser)
    {
        $this->searchUser = $searchUser;
    }

    public function loadUserByUsername($username)
    {
        $searchUserResponse = $this->searchUser->__invoke(new SearchUserByEmailRequest($username));

        if (!$searchUserResponse) {
            throw new UsernameNotFoundException('No user found for username '.$username);
        }

        return new SymfonyUser(
            $searchUserResponse->getEmail(),
            $searchUserResponse->getPassword(),
            $searchUserResponse->getFirstName(),
            $searchUserResponse->getLastName(),
            $searchUserResponse->getRole()
        );
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof SymfonyUser) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }
        return $this->loadUserByUsername($user->getUsername());
    }


    public function supportsClass($class)
    {
        return SymfonyUser::class === $class;
    }
}