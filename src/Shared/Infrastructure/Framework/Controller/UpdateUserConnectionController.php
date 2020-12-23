<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserConnectionRequest;
use LaSalle\StudentTeacher\User\Application\Service\UpdateUserConnectionService;
use Symfony\Component\HttpFoundation\Response;

final class UpdateUserConnectionController extends AbstractFOSRestController
{
    public function __construct(private UpdateUserConnectionService $updateUserConnection)
    {
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
