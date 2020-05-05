<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Application\Service;

use LaSalle\StudentTeacher\Resource\Application\Exception\CategoryAlreadyExists;
use LaSalle\StudentTeacher\Resource\Application\Request\CreateCategoryRequest;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Category;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CategoryRepository;
use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Application\Exception\PermissionDeniedException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;

final class CreateCategory
{
    private CategoryRepository $categoryRepository;
    private UserRepository $userRepository;

    public function __construct(CategoryRepository $categoryRepository, UserRepository $userRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(CreateCategoryRequest $request)
    {
        $this->ensureRequestAuthorCanExecute($request->getRequestAuthorId());

        $this->ensureCategoryNotExistsWithThisName($request->getName());

        $category = new Category($this->categoryRepository->nextIdentity(), $request->getName());

        $this->categoryRepository->save($category);
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

    private function ensureCategoryNotExistsWithThisName(string $name): void
    {
        if (null !== $this->categoryRepository->ofName($name)) {
            throw new CategoryAlreadyExists();
        };
    }
}