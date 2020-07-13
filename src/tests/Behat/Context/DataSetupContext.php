<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;
use Test\LaSalle\StudentTeacher\User\Builder\RefreshTokenBuilder;
use Test\LaSalle\StudentTeacher\User\Builder\UserBuilder;

class DataSetupContext implements Context, SnippetAcceptingContext
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Given there are Users with the following details:
     */
    public function thereAreUsersWithTheFollowingDetails(TableNode $users)
    {
        foreach ($users->getColumnsHash() as $key => $val) {

            $firstName = new Name($val['firstName']);
            $lastName = new Name($val['lastName']);
            $email = new Email($val['email']);
            $password = Password::fromPlainPassword($val['password']);

            $user = (new UserBuilder())
                ->withFirstName($firstName)
                ->withLastName($lastName)
                ->withEmail($email)
                ->withPassword($password)
                ->withEnabled(true)
                ->build();

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }

    /**
     * @Given there are RefreshTokens with the following details:
     */
    public function thereAreRefreshTokensWithTheFollowingDetails(TableNode $refreshTokens)
    {
        foreach ($refreshTokens->getColumnsHash() as $key => $val) {

            $token = new Token($val['refreshToken']);
            //$userId = new Uuid($val['userId']);
            //$expirationDate = new DateTimeImmutable($val['expirationDate']);

            $refreshToken = (new RefreshTokenBuilder())
                ->withRefreshToken($token)
            //    ->withUserId($userId)
            //    ->withExpirationDate($expirationDate)
                ->build();

            $this->entityManager->persist($refreshToken);
            $this->entityManager->flush();
        }
    }
}
