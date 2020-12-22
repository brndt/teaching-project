<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\CoursePermission;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\TestResource;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\TestResourceStudentAnswer;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Unit;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\VideoResource;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\VideoResourceStudentAnswer;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\StudentTestAnswer;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\TestQuestion;
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
            $created = isset($val['created']) ? new DateTimeImmutable($val['created']) : new DateTimeImmutable();
            $image = $val['image'] ?? null;
            $education = $val['education'] ?? null;
            $experience = $val['experience'] ?? null;
            $confirmationToken = isset($val['confirmationToken']) ? new Token($val['confirmationToken']) : null;
            $expirationDate = isset($val['expirationDate']) ? new DateTimeImmutable($val['expirationDate']) : null;

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

    /**
     * @Given there are test resources with the following details:
     */
    public function thereAreTestResourcesWithTheFollowingDetails(TableNode $resources)
    {
        foreach ($resources->getColumnsHash() as $key => $val) {
            $id = new Uuid($val['id']);
            $unitId = new Uuid($val['unitId']);
            $name = $val['name'];
            $description = $val['description'];
            $content = $val['content'];
            $created = new DateTimeImmutable($val['created']);
            $modified = new DateTimeImmutable($val['modified']);
            $status = new Status($val['status']);
            $questions = array_map($this->questionMaker(), json_decode($val['questions'], true));

            $testResource = new TestResource($id, $unitId, $name, $description, $content, $created, $modified, $status, ...$questions);

            $this->entityManager->persist($testResource);
            $this->entityManager->flush();
        }
    }

    /**
     * @Given there are test resource student answers with the following details:
     */
    public function thereAreTestResourceStudentAnswersWithTheFollowingDetails(TableNode $resources)
    {
        foreach ($resources->getColumnsHash() as $key => $val) {
            $id = new Uuid($val['id']);
            $resourceId = new Uuid($val['resourceId']);
            $studentId = new Uuid($val['studentId']);
            $points = $val['points'];
            $teacherComment = $val['teacherComment'];
            $created = new DateTimeImmutable($val['created']);
            $modified = new DateTimeImmutable($val['modified']);
            $until = isset($val['until']) ? new DateTimeImmutable($val['until']) : null;
            $status = new Status($val['status']);
            $assumptions = array_map($this->assumptionMaker(), json_decode($val['assumptions'], true));

            $testResource = new TestResourceStudentAnswer($id, $resourceId, $studentId, $points, $teacherComment, $created, $modified, $until, $status, ...$assumptions);

            $this->entityManager->persist($testResource);
            $this->entityManager->flush();
        }
    }

    /**
     * @Given there are video resource student answers with the following details:
     */
    public function thereAreVideoResourceStudentAnswersWithTheFollowingDetails(TableNode $resources)
    {
        foreach ($resources->getColumnsHash() as $key => $val) {
            $id = new Uuid($val['id']);
            $resourceId = new Uuid($val['resourceId']);
            $studentId = new Uuid($val['studentId']);
            $points = $val['points'];
            $teacherComment = $val['teacherComment'];
            $created = new DateTimeImmutable($val['created']);
            $modified = new DateTimeImmutable($val['modified']);
            $until = isset($val['until']) ? new DateTimeImmutable($val['until']) : null;
            $status = new Status($val['status']);
            $studentAnswer = $val['studentAnswer'];

            $testResource = new VideoResourceStudentAnswer($id, $resourceId, $studentId, $points, $teacherComment, $created, $modified, $until, $status, $studentAnswer);

            $this->entityManager->persist($testResource);
            $this->entityManager->flush();
        }
    }

    /**
     * @Given there are video resources with the following details:
     */
    public function thereAreVideoResourcesWithTheFollowingDetails(TableNode $resources)
    {
        foreach ($resources->getColumnsHash() as $key => $val) {
            $id = new Uuid($val['id']);
            $unitId = new Uuid($val['unitId']);
            $name = $val['name'];
            $description = $val['description'];
            $content = $val['content'];
            $created = new DateTimeImmutable($val['created']);
            $modified = new DateTimeImmutable($val['modified']);
            $status = new Status($val['status']);
            $videoURL = $val['videoURL'];
            $videoDescription = $val['videoDescription'];

            $videoResource = new VideoResource($id, $unitId, $name, $description, $content, $created, $modified, $status, $videoURL, $videoDescription);

            $this->entityManager->persist($videoResource);
            $this->entityManager->flush();
        }
    }

    /**
     * @Given there are course permissions with the following details:
     */
    public function thereAreCoursePermissionsWithTheFollowingDetails(TableNode $coursePermissions)
    {
        foreach ($coursePermissions->getColumnsHash() as $key => $val) {
            $id = new Uuid($val['id']);
            $courseId = new Uuid($val['courseId']);
            $studentId = new Uuid($val['studentId']);
            $created = new DateTimeImmutable($val['created']);
            $modified = new DateTimeImmutable($val['modified']);
            $until = null;
            $status = new Status($val['status']);

            $unit = new CoursePermission($id, $courseId, $studentId, $created, $modified, $until, $status);

            $this->entityManager->persist($unit);
            $this->entityManager->flush();
        }
    }

    private function questionMaker(): callable
    {
        return static function (array $values): TestQuestion {
            return TestQuestion::fromValues($values);
        };
    }

    private function assumptionMaker(): callable
    {
        return static function (array $values): StudentTestAnswer {
            return StudentTestAnswer::fromValues($values);
        };
    }
}
