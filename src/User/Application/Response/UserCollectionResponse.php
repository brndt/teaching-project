<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Response;

use LaSalle\StudentTeacher\Shared\Domain\Collection;

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