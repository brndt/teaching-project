<?php


namespace LaSalle\StudentTeacher\Domain;


use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    /**
     * @test
     */
    //@expectedException Error
    public function throwErrorOnEmptyInstance()
    {
        $this->expectExceptionMessage("Invalid Role parameter");
        $roleTemp = new Role("admin");
    }

    /**
     * @test
     */

    public function adminRole()
    {
        $roleTemp = new Role("ROLE_ADMIN");
        $this->assertEquals("ROLE_ADMIN",$roleTemp->toString());
    }

}