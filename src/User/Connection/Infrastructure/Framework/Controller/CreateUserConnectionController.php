<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Connection\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\Connection\Application\Request\CreateUserConnectionRequest;
use LaSalle\StudentTeacher\User\Connection\Application\Service\CreateUserConnectionService;
use Symfony\Component\HttpFoundation\Response;

final class CreateUserConnectionController extends AbstractFOSRestController
{
    public function __construct(private CreateUserConnectionService $userConnection)
    {
    }

    /**
     * @Rest\Post("/api/v1/users/{userId}/connections")
     * @RequestParam(name="friendId")
     */
    public function postAction(ParamFetcher $paramFetcher, string $userId): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $friendId = $paramFetcher->get('friendId');

        ($this->userConnection)(new CreateUserConnectionRequest($requestAuthorId, $userId, $friendId));

        return $this->handleView(
            $this->view(['message' => 'Request has been successfully sent to this user'], Response::HTTP_CREATED)
        );
    }
}
