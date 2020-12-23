<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Service;

use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Unit;
use LaSalle\StudentTeacher\Resource\Domain\Exception\UnitAlreadyExistsException;
use LaSalle\StudentTeacher\Resource\Domain\Exception\UnitNotFoundException;
use LaSalle\StudentTeacher\Resource\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class UnitService
{
    public function __construct(private UnitRepository $repository)
    {
    }

    public function findUnit(Uuid $id): Unit
    {
        $unit = $this->repository->ofId($id);
        if (null === $unit) {
            throw new UnitNotFoundException();
        }
        return $unit;
    }

    public function ensureUnitNotExistsWithThisName(string $unitName): void
    {
        $unit = $this->repository->ofName($unitName);
        if (null !== $unit) {
            throw new UnitAlreadyExistsException();
        }
    }
}
