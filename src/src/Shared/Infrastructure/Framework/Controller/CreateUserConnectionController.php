<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\Application\Request\CreateUserConnectionRequest;
use LaSalle\StudentTeacher\User\Application\Service\CreateUserConnection;
use Symfony\Component\HttpFoundation\Response;

final class CreateUserConnectionController extends AbstractFOSRestController
{
    private CreateUserConnection $userConnection;

    public function __construct(CreateUserConnection $userConnection)
    {
        $this->userConnection = $userConnection;
    }

    /**
     * @Rest\Post("/api/v1/users/{userId}/connections")
     * @RequestParam(name="friend_id")
     */
    public function postAction(ParamFetcher $paramFetcher, string $userId): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $friendId = $paramFetcher->get('friend_id');

        ($this->userConnection)(new CreateUserConnectionRequest($requestAuthorId, $userId, $friendId));

        return $this->handleView(
            $this->view(['message' => 'Request has been successfully sent to this user'], Response::HTTP_CREATED)
        );
    }
}