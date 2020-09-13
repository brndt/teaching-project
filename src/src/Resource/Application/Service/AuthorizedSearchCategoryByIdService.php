<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchCategoryByIdRequest;
use LaSalle\StudentTeacher\Resource\Application\Response\CategoryResponse;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Category;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CoursePermissionRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CategoryService;
use LaSalle\StudentTeacher\Resource\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class AuthorizedSearchCategoryByIdService
{
    private CategoryRepository $categoryRepository;
    private CourseService $courseService;
    private AuthorizationService $authorizationService;
    private CategoryService $categoryService;
    private UserService $userService;

    public function __construct(
        CategoryRepository $categoryRepository,
        UserRepository $userRepository,
        AuthorizationService $authorizationService
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->userService = new UserService($userRepository);
        $this->categoryService = new CategoryService($categoryRepository);
        $this->authorizationService = $authorizationService;
    }

    public function __invoke(AuthorizedSearchCategoryByIdRequest $request): CategoryResponse
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);
        $this->authorizationService->ensureRequestAuthorIsAdmin($requestAuthor);

        $categoryId = new Uuid($request->getCategoryId());
        $category = $this->categoryService->findCategory($categoryId);

        return $this->buildResponse($category);
    }

    private function buildResponse(Category $category): CategoryResponse
    {
        return new CategoryResponse(
            $category->getId()->toString(),
            $category->getName(),
            $category->getStatus()->value(),
        );
    }

}
