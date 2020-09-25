<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Service;

use LaSalle\StudentTeacher\Resource\Domain\Aggregate\ResourceStudentAnswer;
use LaSalle\StudentTeacher\Resource\Domain\Exception\ResourceStudentAnswerNotFoundException;
use LaSalle\StudentTeacher\Resource\Domain\Exception\StudentAnswerAlreadyExists;
use LaSalle\StudentTeacher\Resource\Domain\Repository\ResourceStudentAnswerRepository;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class ResourceStudentAnswerService
{
    private ResourceStudentAnswerRepository $repository;

    public function __construct(ResourceStudentAnswerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findResourceStudentAnswer(Uuid $resourceId, Uuid $studentId): ResourceStudentAnswer
    {
        $criteria = new Criteria(
            Filters::fromValues(
                [
                    ['field' => 'resourceId', 'operator' => '=', 'value' => $resourceId->toString()],
                    ['field' => 'studentId', 'operator' => '=', 'value' => $studentId->toString()]
                ]
            ), Order::fromValues(null, null), Operator::fromValue(null), null, null
        );
        $resourceStudentAnswer = $this->repository->matching($criteria);

        if (true === empty($resourceStudentAnswer)) {
            throw new ResourceStudentAnswerNotFoundException();
        }

        return $resourceStudentAnswer[0];
    }

    public function ensureStudentAnswerNotExists(Uuid $studentId, Uuid $resourceId)
    {
        $criteria = new Criteria(
            Filters::fromValues(
                [
                    ['field' => 'resourceId', 'operator' => '=', 'value' => $resourceId->toString()],
                    ['field' => 'studentId', 'operator' => '=', 'value' => $studentId->toString()]
                ]
            ), Order::fromValues(null, null), Operator::fromValue(null), null, null
        );
        $studentAnswer = $this->repository->matching($criteria);
        if (false === empty($studentAnswer)) {
            throw new StudentAnswerAlreadyExists();
        }
        return $studentAnswer;
    }
}
