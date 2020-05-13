<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\CreateCategoryRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\CreateCategory;
use Symfony\Component\HttpFoundation\Response;

final class CreateCategoryController extends AbstractFOSRestController
{
    private CreateCategory $createCategory;

    public function __construct(CreateCategory $createCategory)
    {
        $this->createCategory = $createCategory;
    }

    /**
     * @Rest\Post("/api/v1/panel/categories")
     * @RequestParam(name="category_name")
     * @RequestParam(name="status")
     */
    public function postAction(ParamFetcher $paramFetcher): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $categoryName = $paramFetcher->get('category_name');
        $status = $paramFetcher->get('status');

        ($this->createCategory)(new CreateCategoryRequest($requestAuthorId, $categoryName, $status));

        return $this->handleView(
            $this->view(['message' => 'Category has been successfully created'], Response::HTTP_CREATED)
        );
    }
}