<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\UpdateCourseRequest;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CategoryService;
use LaSalle\StudentTeacher\Resource\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class UpdateCourseService
{
    private CourseService $courseService;
    private UserService $userService;
    private CategoryService $categoryService;

    public function __construct(
        private CourseRepository $courseRepository,
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        private AuthorizationService $authorizationService
    ) {
        $this->courseService = new CourseService($courseRepository);
        $this->userService = new UserService($userRepository);
        $this->categoryService = new CategoryService($categoryRepository);
    }

    public function __invoke(UpdateCourseRequest $request): void
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $teacherId = new Uuid($request->getTeacherId());
        $this->userService->findUser($teacherId);

        $categoryId = new Uuid($request->getCategoryId());
        $this->categoryService->findCategory($categoryId);

        $courseId = new Uuid($request->getId());
        $course = $this->courseService->findCourse($courseId);

        $this->authorizationService->ensureUserHasPermissionsToManageCourse($requestAuthor, $course);

        $course->setDescription($request->getDescription());
        $course->setLevel($request->getLevel());
        $course->setName($request->getName());
        $course->setCategoryId($categoryId);
        $course->setStatus(new Status($request->getStatus()));
        $course->setModified($request->getModified());

        $this->courseRepository->save($course);
    }
}
