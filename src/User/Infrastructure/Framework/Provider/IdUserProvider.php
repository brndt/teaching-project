<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Provider;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\SearchUserByIdRequest;
use LaSalle\StudentTeacher\User\Application\Service\SearchUserById;
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

    public function loadUserByUsername($id)
    {
        try {
            $searchUserResponse = ($this->searchUser)(new SearchUserByIdRequest($id));
        } catch (UserNotFoundException | InvalidArgumentValidationException $exception) {
            throw new UsernameNotFoundException(sprintf('No user found for id ' . $id));
        }

        return new SymfonyUser(
            $searchUserResponse->getId(),
            $searchUserResponse->getEmail(),
            $searchUserResponse->getPassword(),
            SymfonyUser::processValueToSymfonyRole($searchUserResponse->getRoles())
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