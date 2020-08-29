<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Request\CreateCategoryRequest;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Category;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CategoryService;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class CreateCategoryService
{
    private CategoryRepository $categoryRepository;
    private CategoryService $categoryService;
    private UserService $userService;
    private AuthorizationService $authorizationService;

    public function __construct(
        CategoryRepository $categoryRepository,
        UserRepository $userRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryService = new CategoryService($categoryRepository);
        $this->userService = new UserService($userRepository);
        $this->authorizationService = new AuthorizationService();
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
