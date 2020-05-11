<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\CreateCourseRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\CreateCourseService;
use LaSalle\StudentTeacher\User\Application\Request\CreateUserConnectionRequest;
use LaSalle\StudentTeacher\User\Application\Service\CreateUserConnectionService;
use Symfony\Component\HttpFoundation\Response;

final class CreateCourseController extends AbstractFOSRestController
{
    private CreateCourseService $createCourse;

    public function __construct(CreateCourseService $createCourse)
    {
        $this->createCourse = $createCourse;
    }

    /**
     * @Rest\Post("/api/v1/courses")
     * @RequestParam(name="teacher_id")
     * @RequestParam(name="category_id")
     * @RequestParam(name="name")
     * @RequestParam(name="description", nullable=true)
     * @RequestParam(name="level")
     * @RequestParam(name="status")
     */
    public function postAction(ParamFetcher $paramFetcher): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $teacherId = $paramFetcher->get('teacher_id');
        $categoryId = $paramFetcher->get('category_id');
        $name = $paramFetcher->get('name');
        $description = $paramFetcher->get('description');
        $level = $paramFetcher->get('level');
        $status = $paramFetcher->get('status');

        ($this->createCourse)(new CreateCourseRequest($requestAuthorId, $teacherId, $categoryId, $name, $description, $level, new \DateTimeImmutable(), null, $status));

        return $this->handleView(
            $this->view(['message' => 'Course has been successfully created'], Response::HTTP_CREATED)
        );
    }
}