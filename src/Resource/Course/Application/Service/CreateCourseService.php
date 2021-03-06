<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Course\Application\Service;

use LaSalle\StudentTeacher\Resource\Category\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Resource\Category\Domain\Service\CategoryService;
use LaSalle\StudentTeacher\Resource\Course\Application\Request\CreateCourseRequest;
use LaSalle\StudentTeacher\Resource\Course\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Resource\Course\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\User\Domain\Service\UserService;

final class CreateCourseService
{
    private UserService $userService;
    private CategoryService $categoryService;

    public function __construct(
        CategoryRepository $categoryRepository,
        private CourseRepository $courseRepository,
        UserRepository $userRepository,
        private AuthorizationService $authorizationService
    ) {
        $this->categoryService = new CategoryService($categoryRepository);
        $this->userService = new UserService($userRepository);
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
