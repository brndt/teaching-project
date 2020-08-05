<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\User\Application\Request\SearchUserConnectionByCriteriaRequest;
use LaSalle\StudentTeacher\User\Application\Service\SearchUserConnectionByIdService;
use Symfony\Component\HttpFoundation\Response;

final class SearchUserConnectionController extends AbstractFOSRestController
{
    private SearchUserConnectionByIdService $searchConnection;

    public function __construct(SearchUserConnectionByIdService $searchConnections)
    {
        $this->searchConnection = $searchConnections;
    }

    /**
     * @Rest\Get("/api/v1/users/{userId}/connections/{friendId}")
     */
    public function getAction(string $userId, string $friendId): Response
    {
        $requestAuthorId = $this->getUser()->getId();

        $connection = ($this->searchConnection)(
            new SearchUserConnectionByCriteriaRequest(
                $requestAuthorId,
                $userId,
                $friendId
            )
        );

        return $this->handleView(
            $this->view($connection, Response::HTTP_OK)
        );
    }
}
