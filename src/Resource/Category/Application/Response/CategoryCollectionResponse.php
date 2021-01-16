<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Category\Application\Response;

use LaSalle\StudentTeacher\Shared\Domain\Collection;

final class CategoryCollectionResponse extends Collection
{
    public function __construct(CategoryResponse ...$categoryResponses)
    {
        parent::__construct($categoryResponses);
    }

    protected function type(): string
    {
        return CategoryResponse::class;
    }
}