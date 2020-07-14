<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\Resource\Application\Exception\CategoryNotFound;
use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchCategoryByIdRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\AuthorizedSearchCategoryByIdService;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizedSearchCategoryByIdController extends AbstractFOSRestController
{
    private AuthorizedSearchCategoryByIdService $searchCategoryByIdService;

    public function __construct(AuthorizedSearchCategoryByIdService $searchCategoryByIdService)
    {
        $this->searchCategoryByIdService = $searchCategoryByIdService;
    }

    /**
     * @Rest\Get("/api/v1/panel/categories/{categoryId}")
     */
    public function getAction(string $categoryId): Response
    {
        $requestAuthorId = $this->getUser()->getId();

        try {
            $category = ($this->searchCategoryByIdService)(
                new AuthorizedSearchCategoryByIdRequest($requestAuthorId, $categoryId)
            );
        } catch (CategoryNotFound $exception) {
            return $this->handleView(
                $this->view(null,Response::HTTP_NO_CONTENT)
            );
        }

        return $this->handleView(
            $this->view($category, Response::HTTP_OK)
        );
    }
}
