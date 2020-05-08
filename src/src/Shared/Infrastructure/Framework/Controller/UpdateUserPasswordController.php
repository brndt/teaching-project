<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\Application\Exception\OldPasswordIncorrectException;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserPasswordRequest;
use LaSalle\StudentTeacher\User\Application\Service\UpdateUserPassword;
use Symfony\Component\HttpFoundation\Response;

final class UpdateUserPasswordController extends AbstractFOSRestController
{
    private UpdateUserPassword $updateUserPassword;

    public function __construct(UpdateUserPassword $updatePassword)
    {
        $this->updateUserPassword = $updatePassword;
    }

    /**
     * @Rest\Patch("/api/v1/users/{userId}/password")
     * @RequestParam(name="old_password")
     * @RequestParam(name="new_password")
     */
    public function patchAction(ParamFetcher $paramFetcher, string $userId): Response
    {
        $oldPassword = $paramFetcher->get('old_password');
        $newPassword = $paramFetcher->get('new_password');

        ($this->updateUserPassword)(new UpdateUserPasswordRequest($userId, $oldPassword, $newPassword));

        return $this->handleView(
            $this->view(['message' => 'Your account has been successfully changed'], Response::HTTP_OK)
        );
    }
}