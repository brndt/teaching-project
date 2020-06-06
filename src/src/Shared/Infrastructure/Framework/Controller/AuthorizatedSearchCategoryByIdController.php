<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizatedSearchCategoryByIdRequest;
use LaSalle\StudentTeacher\Resource\Application\Service\AuthorizatedSearchCategoryByIdService;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizatedSearchCategoryByIdController extends AbstractFOSRestController
{
    private AuthorizatedSearchCategoryByIdService $searchCategoryByIdService;

    public function __construct(AuthorizatedSearchCategoryByIdService $searchCategoryByIdService)
    {
        $this->searchCategoryByIdService = $searchCategoryByIdService;
    }

    /**
     * @Rest\Get("/api/v1/panel/categories/{categoryId}")
     */
    public function getAction(string $categoryId): Response
    {
        $requestAuthorId = $this->getUser()->getId();

        $category = ($this->searchCategoryByIdService)(
            new AuthorizatedSearchCategoryByIdRequest($requestAuthorId, $categoryId)
        );

        return $this->handleView(
            $this->view($category, Response::HTTP_OK)
        );
    }
}
