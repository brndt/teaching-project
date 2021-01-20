<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\User\Application\Request\UpdateUserPasswordRequest;
use LaSalle\StudentTeacher\User\User\Application\Service\UpdateUserPasswordService;
use Symfony\Component\HttpFoundation\Response;

final class UpdateUserPasswordController extends AbstractFOSRestController
{
    public function __construct(private UpdateUserPasswordService $updateUserPassword)
    {
    }

    /**
     * @Rest\Patch("/api/v1/users/{userId}/password")
     * @RequestParam(name="oldPassword")
     * @RequestParam(name="newPassword")
     */
    public function patchAction(ParamFetcher $paramFetcher, string $userId): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $oldPassword = $paramFetcher->get('oldPassword');
        $newPassword = $paramFetcher->get('newPassword');

        ($this->updateUserPassword)(
            new UpdateUserPasswordRequest($requestAuthorId, $userId, $oldPassword, $newPassword)
        );

        return $this->handleView(
            $this->view(['message' => 'Your account has been successfully changed'], Response::HTTP_OK)
        );
    }
}
