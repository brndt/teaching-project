<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Domain\Service;

use LaSalle\StudentTeacher\Resource\Domain\Exception\StudentAnswerAlreadyExists;
use LaSalle\StudentTeacher\Resource\Domain\Repository\RecourseStudentAnswerRepository;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;

final class ResourceStudentAnswerService
{
    private RecourseStudentAnswerRepository $repository;

    public function __construct(RecourseStudentAnswerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function ensureStudentAnswerNotExists(Uuid $studentId, Uuid $recourseId)
    {
        $criteria = new Criteria(
            Filters::fromValues(
                [
                    ['field' => 'recourseId', 'operator' => '=', 'value' => $recourseId->toString()],
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
