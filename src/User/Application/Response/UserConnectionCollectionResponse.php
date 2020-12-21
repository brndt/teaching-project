<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Application\Response;

use LaSalle\StudentTeacher\Shared\Domain\Collection;

final class UserConnectionCollectionResponse extends Collection
{
    public function __construct(UserConnectionResponse ...$connectionResponses)
    {
        parent::__construct($connectionResponses);
    }

    protected function type(): string
    {
        return UserConnectionResponse::class;
    }
}