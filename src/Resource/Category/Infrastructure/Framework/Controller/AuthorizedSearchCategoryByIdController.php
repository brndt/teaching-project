<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Resource\Category\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\Resource\Category\Application\Request\AuthorizedSearchCategoryByIdRequest;
use LaSalle\StudentTeacher\Resource\Category\Application\Service\AuthorizedSearchCategoryByIdService;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizedSearchCategoryByIdController extends AbstractFOSRestController
{
    public function __construct(private AuthorizedSearchCategoryByIdService $searchCategoryByIdService)
    {
    }

    /**
     * @Rest\Get("/api/v1/panel/categories/{categoryId}")
     */
    public function __invoke(string $categoryId): Response
    {
        $requestAuthorId = $this->getUser()->getId();

        $category = ($this->searchCategoryByIdService)(
            new AuthorizedSearchCategoryByIdRequest($requestAuthorId, $categoryId)
        );

        return $this->handleView(
            $this->view($category, Response::HTTP_OK)
        );
    }
}
