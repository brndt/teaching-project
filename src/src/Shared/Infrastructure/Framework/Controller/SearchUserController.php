<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\SearchUsersByCriteriaRequest;
use LaSalle\StudentTeacher\User\Application\Service\SearchUsersByCriteriaService;
use Symfony\Component\HttpFoundation\Response;

final class SearchUserController extends AbstractFOSRestController
{
    private SearchUsersByCriteriaService $searchUsersByCriteria;

    public function __construct(SearchUsersByCriteriaService $searchUsersByCriteria)
    {
        $this->searchUsersByCriteria = $searchUsersByCriteria;
    }

    /**
     * @Rest\Get("/api/v1/users/{userId}")
     */
    public function getAction(string $userId): Response
    {
        $filters = [['field' => 'id', 'operator' => '=', 'value' => $userId]];

        try {
            $userResponse = ($this->searchUsersByCriteria)(
                new SearchUsersByCriteriaRequest($filters, null, null, null, null, null)
            );
        } catch(UserNotFoundException $exception) {
            return $this->handleView(
                $this->view(null,Response::HTTP_NO_CONTENT)
            );
        }

        return $this->handleView($this->view($userResponse->getIterator()->current(), Response::HTTP_OK));
    }
}
