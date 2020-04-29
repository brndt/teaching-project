<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Provider;

use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Service\SearchUserByEmail;
use LaSalle\StudentTeacher\User\Application\Service\SearchUserCredentialsByCriteria;
use LaSalle\StudentTeacher\User\Infrastructure\Framework\Entity\SymfonyUser;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class EmailUserProvider implements UserProviderInterface
{
    private SearchUserCredentialsByCriteria $searchUser;

    public function __construct(SearchUserCredentialsByCriteria $searchUser)
    {
        $this->searchUser = $searchUser;
    }

    public function loadUserByUsername($email)
    {
        $filters = [['field' => 'email', 'operator' => '=', 'value' => $email]];
        $criteria = new Criteria(Filters::fromValues($filters), Order::fromValues(null, null), null, null);

        try {
            $UserCollectionResponse = ($this->searchUser)($criteria);
        } catch (UserNotFoundException $exception) {
            throw new UsernameNotFoundException(sprintf('No user found for email ' . $email));
        }

        $userResponse = $UserCollectionResponse->getIterator()->current();

        return new SymfonyUser(
            $userResponse->getId(),
            $userResponse->getEmail(),
            $userResponse->getPassword(),
            SymfonyUser::processValueToSymfonyRole($userResponse->getRoles()),
            $userResponse->getEnabled()
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