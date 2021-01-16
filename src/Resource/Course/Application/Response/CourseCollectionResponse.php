<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Course\Application\Response;

use LaSalle\StudentTeacher\Shared\Domain\Collection;

final class CourseCollectionResponse extends Collection
{
    public function __construct(CourseResponse ...$courseResponses)
    {
        parent::__construct($courseResponses);
    }

    protected function type(): string
    {
        return CourseResponse::class;
    }
}