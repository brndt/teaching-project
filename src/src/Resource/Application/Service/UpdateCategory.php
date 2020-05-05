<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Exception\CategoryAlreadyExists;
use LaSalle\StudentTeacher\Resource\Application\Exception\CategoryNotFound;
use LaSalle\StudentTeacher\Resource\Application\Request\CreateCategoryRequest;
use LaSalle\StudentTeacher\Resource\Application\Request\UpdateCategoryRequest;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Category;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\FilterOperator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;

final class UpdateCategory
{
    private CategoryRepository $categoryRepository;
    private UserRepository $userRepository;

    public function __construct(CategoryRepository $categoryRepository, UserRepository $userRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(UpdateCategoryRequest $request)
    {
        $this->ensureRequestAuthorCanExecute($request->getRequestAuthorId());

        $categoryId = $this->createIdFromPrimitive($request->getCategoryId());

        $category = $this->categoryRepository->ofId($categoryId);

        $this->ensureCategoryExists($category);

        $this->ensureCategoryNotExistsWithThisName($request->getNewName(), $categoryId);

        $category->setName($request->getNewName());

        $this->categoryRepository->save($category);
    }

    private function ensureCategoryExists(?Category $category) {
        if (null === $category) {
            throw new CategoryNotFound();
        }
    }

    private function createIdFromPrimitive(string $uuid): Uuid
    {
        try {
            return new Uuid($uuid);
        } catch (InvalidUuidException $error) {
            throw new InvalidArgumentValidationException($error->getMessage());
        }
    }

    private function ensureRequestAuthorCanExecute(string $requestAuthorId): void
    {
        $author = $this->userRepository->ofId($this->createIdFromPrimitive($requestAuthorId));
        if (false === in_array('admin', $author->getRoles()->toArrayOfPrimitives())) {
            throw new PermissionDeniedException();
        }
    }

    private function ensureCategoryNotExistsWithThisName(string $name, Uuid $id): void
    {
        $category = $this->categoryRepository->ofName($name);
        if (null !== $category && $id->toString() !== $category->getId()->toString()) {
            throw new CategoryAlreadyExists();
        };
    }
}