<?php
declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Response;

use LaSalle\StudentTeacher\Shared\Domain\Collection;

final class ResourceCollectionResponse extends Collection
{
    public function __construct(ResourceResponse ...$resourceResponses)
    {
        parent::__construct($resourceResponses);
    }

    protected function type(): string
    {
        return ResourceResponse::class;
    }
}
