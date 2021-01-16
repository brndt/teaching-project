<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use DateTimeImmutable;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Course\Application\CreateCourseRequest;
use LaSalle\StudentTeacher\Resource\Course\Application\CreateCourseService;
use Symfony\Component\HttpFoundation\Response;

final class CreateCourseController extends AbstractFOSRestController
{
    public function __construct(private CreateCourseService $createCourse)
    {
    }

    /**
     * @Rest\Post("/api/v1/panel/courses")
     * @RequestParam(name="teacherId")
     * @RequestParam(name="categoryId")
     * @RequestParam(name="name")
     * @RequestParam(name="description", nullable=true)
     * @RequestParam(name="level")
     * @RequestParam(name="status")
     */
    public function postAction(ParamFetcher $paramFetcher): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $teacherId = $paramFetcher->get('teacherId');
        $categoryId = $paramFetcher->get('categoryId');
        $name = $paramFetcher->get('name');
        $description = $paramFetcher->get('description');
        $level = $paramFetcher->get('level');
        $status = $paramFetcher->get('status');

        ($this->createCourse)(
            new CreateCourseRequest(
                $requestAuthorId,
                $teacherId,
                $categoryId,
                $name,
                $description,
                $level,
                new DateTimeImmutable(),
                null,
                $status
            )
        );

        return $this->handleView(
            $this->view(['message' => 'Course has been successfully created'], Response::HTTP_CREATED)
        );
    }
}
