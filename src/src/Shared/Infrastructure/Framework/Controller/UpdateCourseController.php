<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\UpdateCourseRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\UpdateCourseService;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserInformationRequest;
use LaSalle\StudentTeacher\User\Application\Service\UpdateUserInformationService;
use Symfony\Component\HttpFoundation\Response;

final class UpdateCourseController extends AbstractFOSRestController
{
    private UpdateCourseService $updateCourse;

    public function __construct(UpdateCourseService $updateCourse)
    {
        $this->updateCourse = $updateCourse;
    }

    /**
     * @Rest\Patch("/api/v1/panel/courses/{courseId}")
     * @RequestParam(name="teacher_id")
     * @RequestParam(name="category_id")
     * @RequestParam(name="name")
     * @RequestParam(name="description", nullable=true)
     * @RequestParam(name="level")
     * @RequestParam(name="status")
     */
    public function postAction(ParamFetcher $paramFetcher, string $courseId): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $teacherId = $paramFetcher->get('teacher_id');
        $categoryId = $paramFetcher->get('category_id');
        $name = $paramFetcher->get('name');
        $description = $paramFetcher->get('description');
        $level = $paramFetcher->get('level');
        $status = $paramFetcher->get('status');

        ($this->updateCourse)(
            new UpdateCourseRequest($requestAuthorId, $courseId, $teacherId, $categoryId, $name, $description, $level, new \DateTimeImmutable(), $status)
        );

        return $this->handleView(
            $this->view(['message' => 'Course has been successfully updated'], Response::HTTP_OK)
        );
    }
}