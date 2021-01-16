<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Category\Application;

use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Category;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CategoryService;
use LaSalle\StudentTeacher\Resource\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class AuthorizedSearchCategoryByIdService
{
    private CourseService $courseService;
    private CategoryService $categoryService;
    private UserService $userService;

    public function __construct(
        private CategoryRepository $categoryRepository,
        UserRepository $userRepository,
        private AuthorizationService $authorizationService
    ) {
        $this->userService = new UserService($userRepository);
        $this->categoryService = new CategoryService($categoryRepository);
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
