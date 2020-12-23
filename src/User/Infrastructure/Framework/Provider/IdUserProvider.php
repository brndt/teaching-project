<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Provider;

use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\SearchUserCredentialsByIdRequest;
use LaSalle\StudentTeacher\User\Application\Service\SearchUserCredentialsByIdService;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\Entity\SymfonyUser;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class IdUserProvider implements UserProviderInterface
{
    public function __construct(private SearchUserCredentialsByIdService $searchUser)
    {
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof SymfonyUser) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', $user::class));
        }
        return $this->loadUserByUsername($user->getId());
    }

    public function loadUserByUsername($id)
    {
        try {
            $userResponse = ($this->searchUser)(new SearchUserCredentialsByIdRequest($id));
        } catch (UserNotFoundException) {
            throw new UsernameNotFoundException(sprintf('No user found for id ' . $id));
        }

        return new SymfonyUser(
            $userResponse->getId(),
            $userResponse->getEmail(),
            $userResponse->getPassword(),
            SymfonyUser::processValueToSymfonyRole($userResponse->getRoles()),
            $userResponse->getEnabled()
        );
    }

    public function supportsClass($class)
    {
        return SymfonyUser::class === $class;
    }
}