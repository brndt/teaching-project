<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Category\Application\UpdateCategoryRequest;
use LaSalle\StudentTeacher\Resource\Category\Application\UpdateCategoryService;
use Symfony\Component\HttpFoundation\Response;

final class UpdateCategoryController extends AbstractFOSRestController
{
    public function __construct(private UpdateCategoryService $updateCategory)
    {
    }

    /**
     * @Rest\Patch("/api/v1/panel/categories/{categoryId}")
     * @RequestParam(name="name")
     * @RequestParam(name="status")
     */
    public function postAction(ParamFetcher $paramFetcher, string $categoryId): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $name = $paramFetcher->get('name');
        $status = $paramFetcher->get('status');

        ($this->updateCategory)(new UpdateCategoryRequest($requestAuthorId, $categoryId, $name, $status));

        return $this->handleView(
            $this->view(['message' => 'Category has been successfully update'], Response::HTTP_OK)
        );
    }
}
