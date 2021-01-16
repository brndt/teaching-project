<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Category\Application\Request\CreateCategoryRequest;
use LaSalle\StudentTeacher\Resource\Category\Application\Service\CreateCategoryService;
use Symfony\Component\HttpFoundation\Response;

final class CreateCategoryController extends AbstractFOSRestController
{
    public function __construct(private CreateCategoryService $createCategory)
    {
    }

    /**
     * @Rest\Post("/api/v1/panel/categories")
     * @RequestParam(name="name")
     * @RequestParam(name="status")
     */
    public function postAction(ParamFetcher $paramFetcher): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $categoryName = $paramFetcher->get('name');
        $status = $paramFetcher->get('status');

        ($this->createCategory)(new CreateCategoryRequest($requestAuthorId, $categoryName, $status));

        return $this->handleView(
            $this->view(['message' => 'Category has been successfully created'], Response::HTTP_CREATED)
        );
    }
}
