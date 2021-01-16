<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Category\Application;

use LaSalle\StudentTeacher\Resource\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CategoryService;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

final class UpdateCategoryService
{
    private CategoryService $categoryService;
    private UserService $userService;

    public function __construct(
        UserRepository $userRepository,
        private CategoryRepository $categoryRepository,
        private AuthorizationService $authorizationService
    ) {
        $this->userService = new UserService($userRepository);
        $this->categoryService = new CategoryService($categoryRepository);
    }

    public function __invoke(UpdateCategoryRequest $request): void
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);
        $this->authorizationService->ensureRequestAuthorIsAdmin($requestAuthor);

        $categoryId = new Uuid($request->getCategoryId());
        $category = $this->categoryService->findCategory($categoryId);

        $this->categoryService->ensureCategoryNameIsAvailable($category->getName(), $request->getNewName());

        $newStatus = new Status($request->getNewStatus());

        $category->setName($request->getNewName());
        $category->setStatus($newStatus);

        $this->categoryRepository->save($category);
    }
}
