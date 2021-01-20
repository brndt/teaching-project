<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Category\Application\Service;

use LaSalle\StudentTeacher\Resource\Category\Application\Request\CreateCategoryRequest;
use LaSalle\StudentTeacher\Resource\Category\Domain\Aggregate\Category;
use LaSalle\StudentTeacher\Resource\Category\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Resource\Category\Domain\Service\CategoryService;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\User\Domain\Service\UserService;

final class CreateCategoryService
{
    private CategoryService $categoryService;
    private UserService $userService;

    public function __construct(
        private CategoryRepository $categoryRepository,
        UserRepository $userRepository,
        private AuthorizationService $authorizationService
    ) {
        $this->categoryService = new CategoryService($categoryRepository);
        $this->userService = new UserService($userRepository);
    }

    public function __invoke(CreateCategoryRequest $request): void
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $this->authorizationService->ensureRequestAuthorIsAdmin($requestAuthor);

        $this->categoryService->ensureCategoryNotExistsWithThisName($request->getCategoryName());

        $status = new Status($request->getCategoryStatus());

        $category = new Category($this->categoryRepository->nextIdentity(), $request->getCategoryName(), $status);

        $this->categoryRepository->save($category);
    }
}
