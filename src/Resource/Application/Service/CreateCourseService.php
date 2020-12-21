<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\CreateCourseRequest;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CategoryService;
use LaSalle\StudentTeacher\Resource\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class CreateCourseService
{
    private AuthorizationService $authorizationService;
    private UserService $userService;
    private CourseRepository $courseRepository;
    private CategoryService $categoryService;

    public function __construct(CategoryRepository $categoryRepository, CourseRepository $courseRepository, UserRepository $userRepository, AuthorizationService $authorizationService)
    {
        $this->courseRepository = $courseRepository;
        $this->categoryService = new CategoryService($categoryRepository);
        $this->userService = new UserService($userRepository);
        $this->authorizationService = $authorizationService;
    }

    public function __invoke(CreateCourseRequest $request): void
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $teacherId = new Uuid($request->getTeacherId());
        $teacher = $this->userService->findUser($teacherId);

        $categoryId = new Uuid($request->getCategoryId());
        $this->categoryService->findCategory($categoryId);

        $id = $this->courseRepository->nextIdentity();
        $status = new Status($request->getStatus());

        $course = new Course(
            $id,
            $teacherId,
            $categoryId,
            $request->getName(),
            $request->getDescription(),
            $request->getLevel(),
            $request->getCreated(),
            $request->getModified(),
            $status
        );

        $this->authorizationService->ensureRequestAuthorHasPermissionsToCreateCourse($requestAuthor, $teacher);

        $this->courseRepository->save($course);
    }
}
