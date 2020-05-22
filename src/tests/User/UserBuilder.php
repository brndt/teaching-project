<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;

final class UserBuilder
{
    private Uuid $id;
    private Email $email;
    private Password $password;
    private Name $firstName;
    private Name $lastName;
    private Roles $roles;
    private \DateTimeImmutable $created;
    private bool $enabled;
    private ?string $image;
    private ?string $experience;
    private ?string $education;
    private array $eventStream;
    private ?Token $confirmationToken;
    private ?DateTimeImmutable $expirationDate;

    public function __construct()
    {
        $this->id = Uuid::generate();
        $this->email = new Email('hello@example.com');
        $this->password = Password::fromHashedPassword('$2y$10$p7s2XiFvYtXIJIfkZxyyMuMUn7/7TDnDBmCXRXOWienN45/oph1we');
        $this->firstName = new Name('Alex');
        $this->lastName = new Name('Johnson');
        $this->roles = Roles::fromArrayOfPrimitives([Role::ADMIN]);
        $this->created = new DateTimeImmutable();
        $this->image = 'image.jpg';
        $this->experience = '50 years';
        $this->education = 'la salle';
        $this->enabled = true;
        $this->confirmationToken = new Token('confirmation_token');
        $this->expirationDate = new DateTimeImmutable('+1 day');
    }

    public function withId(Uuid $id)
    {
        $this->id = $id;

        return $this;
    }

    public function withRoles(Roles $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function withConfirmationToken(?Token $confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function withExpirationDate(?DateTimeImmutable $expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function withEnabled(bool $enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function build()
    {
        return new User(
            $this->id,
            $this->email,
            $this->password,
            $this->firstName,
            $this->lastName,
            $this->roles,
            $this->created,
            $this->enabled,
            $this->image,
            $this->experience,
            $this->education,
            $this->confirmationToken,
            $this->expirationDate
        );
    }
}