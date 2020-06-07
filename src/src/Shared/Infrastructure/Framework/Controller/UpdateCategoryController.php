<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Resource\Application\Request\UpdateCategoryRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\UpdateCategoryService;
use Symfony\Component\HttpFoundation\Response;

final class UpdateCategoryController extends AbstractFOSRestController
{
    private UpdateCategoryService $updateCategory;

    public function __construct(UpdateCategoryService $updateCategory)
    {
        $this->updateCategory = $updateCategory;
    }

    /**
     * @Rest\Patch("/api/v1/panel/categories/{userId}")
     * @RequestParam(name="name")
     * @RequestParam(name="status")
     */
    public function postAction(ParamFetcher $paramFetcher, string $userId): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $name = $paramFetcher->get('name');
        $status = $paramFetcher->get('status');

        ($this->updateCategory)(new UpdateCategoryRequest($requestAuthorId, $userId, $name, $status));

        return $this->handleView(
            $this->view(['message' => 'Category has been successfully update'], Response::HTTP_OK)
        );
    }
}
