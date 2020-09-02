<?php


namespace LaSalle\StudentTeacher\Resource\Domain\Service;


use LaSalle\StudentTeacher\Resource\Application\Exception\UnitNotFound;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Unit;
use LaSalle\StudentTeacher\Resource\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class UnitService
{
    private UnitRepository $repository;

    public function __construct(UnitRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findUnit(Uuid $id): Unit
    {
        $unit = $this->repository->ofId($id);
        if (null === $unit) {
            throw new UnitNotFound();
        }
        return $unit;
    }
}