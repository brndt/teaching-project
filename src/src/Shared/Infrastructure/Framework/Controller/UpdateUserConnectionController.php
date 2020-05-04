<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\User\Application\Request\CreateUserConnectionRequest;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserConnectionRequest;
use LaSalle\StudentTeacher\User\Application\Service\CreateUserConnection;
use LaSalle\StudentTeacher\User\Application\Service\UpdateUserConnection;
use Symfony\Component\HttpFoundation\Response;

final class UpdateUserConnectionController extends AbstractFOSRestController
{
    private UpdateUserConnection $updateUserConnection;

    public function __construct(UpdateUserConnection $updateUserConnection)
    {
        $this->updateUserConnection = $updateUserConnection;
    }

    /**
     * @Rest\Patch("/api/v1/users/{userId}/connections/{friendId}")
     * @RequestParam(name="status")
     */
    public function postAction(ParamFetcher $paramFetcher, string $userId, string $friendId): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $status = $paramFetcher->get('status');

        ($this->updateUserConnection)(new UpdateUserConnectionRequest($requestAuthorId, $userId, $friendId, $status));

        return $this->handleView(
            $this->view(['message' => 'Connection has been successfully updated'], Response::HTTP_OK)
        );
    }
}