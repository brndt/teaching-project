<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Exception\CategoryAlreadyExists;
use LaSalle\StudentTeacher\Resource\Application\Exception\CategoryNotFound;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Category;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Resource\Domain\ValueObject\Status;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Role;

abstract class CategoryService
{
    protected CategoryRepository $categoryRepository;
    protected UserRepository $userRepository;

    public function __construct(CategoryRepository $categoryRepository, UserRepository $userRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->userRepository = $userRepository;
    }

    protected function createIdFromPrimitive(string $uuid): Uuid
    {
        try {
            return new Uuid($uuid);
        } catch (InvalidUuidException $error) {
            throw new InvalidArgumentException($error->getMessage());
        }
    }

    protected function ensureRequestAuthorIsAdmin(User $requestAuthor): void
    {
        if (false === $requestAuthor->isInRole(new Role(Role::ADMIN))) {
            throw new PermissionDeniedException();
        }
    }

    protected function ensureCategoryNotExistsWithThisName(string $categoryName): void
    {
        $category = $this->categoryRepository->ofName($categoryName);
        if (null !== $category) {
            throw new CategoryAlreadyExists();
        };
    }

    protected function ensureCategoryNameIsAvailable(string $oldCategoryName, string $newCategoryName): void
    {
        $category = $this->categoryRepository->ofName($newCategoryName);
        if (null !== $category && $oldCategoryName !== $category->getName()) {
            throw new CategoryAlreadyExists();
        };
    }

    protected function ensureUserExists(User $user): void
    {
        if (null === $user) {
            throw new UserNotFoundException();
        }
    }

    protected function ensureCategoriesExist(?array $categories): void
    {
        if (true === empty($categories)) {
            throw new CategoryNotFound();
        }
    }

    protected function ensureCategoryExists(?Category $category) {
        if (null === $category) {
            throw new CategoryNotFound();
        }
    }

    protected function createStatusFromPrimitive(string $status)
    {
        try {
            return new Status($status);
        } catch (InvalidArgumentException $error) {
            throw new InvalidArgumentException($error->getMessage());
        }
    }
}