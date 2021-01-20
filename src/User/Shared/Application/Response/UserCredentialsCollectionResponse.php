<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Shared\Application\Response;

use LaSalle\StudentTeacher\Shared\Domain\Collection;
use LaSalle\StudentTeacher\User\Shared\Application\Response\UserCredentialsResponse;

final class UserCredentialsCollectionResponse extends Collection
{
    public function __construct(UserCredentialsResponse ...$userResponse)
    {
        parent::__construct($userResponse);
    }

    protected function type(): string
    {
        return UserCredentialsResponse::class;
    }
}