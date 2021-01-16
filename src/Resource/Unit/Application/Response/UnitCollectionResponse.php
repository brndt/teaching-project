<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Unit\Application\Response;

use LaSalle\StudentTeacher\Shared\Domain\Collection;

final class UnitCollectionResponse extends Collection
{
    public function __construct(UnitResponse ...$unitResponses)
    {
        parent::__construct($unitResponses);
    }

    protected function type(): string
    {
        return UnitResponse::class;
    }
}
