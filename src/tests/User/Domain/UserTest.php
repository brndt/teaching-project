<?php

namespace LaSalle\StudentTeacher\Domain;


use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * @test
     */
    public function zero()
    {
        $userEmpty = new User(
            Uuid::generate(),
            new Email(''),
            Password::fromPlainPassword(''),
            "",
            "",
            Roles::fromArrayOfPrimitives(['']),
            new DateTimeImmutable()
        );
        //var_dump($userTest);
        $this->assertEmpty($userEmpty->getId());
        $this->assertEmpty($userEmpty->getEmail());
        $this->assertEmpty($userEmpty->getPassword());
        $this->assertEmpty($userEmpty->getFirstName());
        $this->assertEmpty($userEmpty->getLastName());
        $this->assertEmpty($userEmpty->getRoles()->toArrayOfRole());
        $this->assertEquals("DateTimeImmutable", $this->get_real_class($userEmpty->getCreated()));

        return $userEmpty;
    }

    /**
     * @test
     * @depends zero
     */
    public function isAnInstanceOfUser(User $userEmpty)
    {
        $this->assertEquals("User", $this->get_real_class($userEmpty));
    }

    private function get_real_class(object $obj)
    {
        $classname = get_class($obj);

        if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
            $classname = $matches[1];
        }

        return $classname;
    }
}