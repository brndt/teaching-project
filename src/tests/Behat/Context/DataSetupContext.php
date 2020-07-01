<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;
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

            $user = (new UserBuilder())
                ->withFirstName($firstName)
                ->withLastName($lastName)
                ->withEmail($email)
                ->build();

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }
}
