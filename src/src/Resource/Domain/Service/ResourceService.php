<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Service;

use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Resource;
use LaSalle\StudentTeacher\Resource\Domain\Exception\ResourceAlreadyExistsException;
use LaSalle\StudentTeacher\Resource\Domain\Exception\ResourceNotFoundException;
use LaSalle\StudentTeacher\Resource\Domain\Repository\ResourceRepository;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class ResourceService
{
    private ResourceRepository $repository;

    public function __construct(ResourceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findResource(Uuid $id): Resource
    {
        $resource = $this->repository->ofId($id);
        if (null === $resource) {
            throw new ResourceNotFoundException();
        }
        return $resource;
    }
}
