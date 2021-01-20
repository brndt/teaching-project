<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Connection\Application\Response;

use LaSalle\StudentTeacher\Shared\Domain\Collection;
use LaSalle\StudentTeacher\User\Connection\Application\Response\UserConnectionResponse;

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