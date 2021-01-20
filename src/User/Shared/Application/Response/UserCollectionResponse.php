<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Shared\Application\Response;

use LaSalle\StudentTeacher\Shared\Domain\Collection;
use LaSalle\StudentTeacher\User\Shared\Application\Response\UserResponse;

final class UserCollectionResponse extends Collection
{
    public function __construct(UserResponse ...$userResponse)
    {
        parent::__construct($userResponse);
    }

    protected function type(): string
    {
        return UserResponse::class;
    }
}