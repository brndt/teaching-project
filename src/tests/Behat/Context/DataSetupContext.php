<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Unit;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Aggregate\UserConnection;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use LaSalle\StudentTeacher\User\Domain\ValueObject\State\Pended;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;
use Test\LaSalle\StudentTeacher\Resource\Builder\CategoryBuilder;
use Test\LaSalle\StudentTeacher\Resource\Builder\CourseBuilder;
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
     * @Given there are users with the following details:
     */
    public function thereAreUsersWithTheFollowingDetails(TableNode $users)
    {
        foreach ($users->getColumnsHash() as $key => $val) {
            $id = new Uuid($val['id']);
            $firstName = new Name($val['firstName']);
            $lastName = new Name($val['lastName']);
            $email = new Email($val['email']);
            $password = Password::fromPlainPassword($val['password']);
            $roles = Roles::fromArrayOfPrimitives([$val['roles']]);
            $created = $val['created'] ? new DateTimeImmutable($val['created']) : new DateTimeImmutable();
            $image = $val['image'];
            $education = $val['education'];
            $experience = $val['experience'];
            $confirmationToken = $val['confirmationToken'] ? new Token($val['confirmationToken']) : null;
            $expirationDate = $val['expirationDate'] ? new DateTimeImmutable($val['expirationDate']) : null;

            $user = (new UserBuilder())
                ->withId($id)
                ->withFirstName($firstName)
                ->withLastName($lastName)
                ->withEmail($email)
                ->withPassword($password)
                ->withRoles($roles)
                ->withCreated($created)
                ->withImage($image)
                ->withEducation($education)
                ->withExperience($experience)
                ->withConfirmationToken($confirmationToken)
                ->withExpirationDate($expirationDate)
                ->withEnabled(true)
                ->build();

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }

    /**
     * @Given there are refresh tokens with the following details:
     */
    public function thereAreRefreshTokensWithTheFollowingDetails(TableNode $refreshTokens)
    {
        foreach ($refreshTokens->getColumnsHash() as $key => $val) {
            $token = new Token($val['refreshToken']);
            $userId = new Uuid($val['userId']);
            $expirationDate = new DateTimeImmutable($val['expirationDate']);

            $refreshToken = (new RefreshTokenBuilder())
                ->withRefreshToken($token)
                ->withUserId($userId)
                ->withExpirationDate($expirationDate)
                ->build();

            $this->entityManager->persist($refreshToken);
            $this->entityManager->flush();
        }
    }

    /**
     * @Given there are user connections with the following details:
     */
    public function thereAreUserConnectionsWithTheFollowingDetails(TableNode $refreshTokens)
    {
        foreach ($refreshTokens->getColumnsHash() as $key => $val) {
            $studentId = new Uuid($val['studentId']);
            $teacherId = new Uuid($val['teacherId']);
            $state = new Pended();
            $specifierId = new Uuid($val['specifierId']);

            $userConnection = new UserConnection($studentId, $teacherId, $state, $specifierId);

            $this->entityManager->persist($userConnection);
            $this->entityManager->flush();
        }
    }

    /**
     * @Given there are categories with the following details:
     */
    public function thereAreCategoriesWithTheFollowingDetails(TableNode $refreshTokens)
    {
        foreach ($refreshTokens->getColumnsHash() as $key => $val) {
            $id = new Uuid($val['id']);
            $name = $val['name'];
            $status = new Status($val['status']);

            $category = (new CategoryBuilder())
                ->withId($id)
                ->withName($name)
                ->withStatus($status)
                ->build();

            $this->entityManager->persist($category);
            $this->entityManager->flush();
        }
    }

    /**
     * @Given there are courses with the following details:
     */
    public function thereAreCoursesWithTheFollowingDetails(TableNode $refreshTokens)
    {
        foreach ($refreshTokens->getColumnsHash() as $key => $val) {
            $id = new Uuid($val['id']);
            $teacherId = new Uuid($val['teacherId']);
            $categoryId = new Uuid($val['categoryId']);
            $name = $val['name'];
            $description = $val['description'];
            $level = $val['level'];
            $created = new DateTimeImmutable($val['created']);
            $modified = new DateTimeImmutable($val['modified']);
            $status = new Status($val['status']);

            $course = (new CourseBuilder())
                ->withId($id)
                ->withTeacherId($teacherId)
                ->withCategoryId($categoryId)
                ->withName($name)
                ->withDescription($description)
                ->withLevel($level)
                ->withCreated($created)
                ->withModified($modified)
                ->withStatus($status)
                ->build();

            $this->entityManager->persist($course);
            $this->entityManager->flush();
        }
    }

    /**
     * @Given there are units with the following details:
     */
    public function thereAreUnitsWithTheFollowingDetails(TableNode $refreshTokens)
    {
        foreach ($refreshTokens->getColumnsHash() as $key => $val) {
            $id = new Uuid($val['id']);
            $courseId = new Uuid($val['courseId']);
            $name = $val['name'];
            $description = $val['description'];
            $level = $val['level'];
            $created = new DateTimeImmutable($val['created']);
            $modified = new DateTimeImmutable($val['modified']);
            $status = new Status($val['status']);

            $unit = new Unit($id, $courseId, $name, $description, $level, $created, $modified, $status);

            $this->entityManager->persist($unit);
            $this->entityManager->flush();
        }
    }
}
