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
    private UpdateUserPassword $updatePassword;

    public function __construct(UpdateUserPassword $updatePassword)
    {
        $this->updatePassword = $updatePassword;
    }

    /**
     * @Rest\Patch("/api/v1/users/{id}/password")
     * @RequestParam(name="oldPassword")
     * @RequestParam(name="newPassword")
     */
    public function patchAction(ParamFetcher $paramFetcher, string $id): Response
    {
        $oldPassword = $paramFetcher->get('oldPassword');
        $newPassword = $paramFetcher->get('newPassword');

        if ($id !== $this->getUser()->getId()) {
            return $this->handleView(
                $this->view(
                    ['message' => 'You don\'t have permission to update this profile'],
                    Response::HTTP_FORBIDDEN
                )
            );
        }

        ($this->updatePassword)(new UpdateUserPasswordRequest($id, $oldPassword, $newPassword));

        return $this->handleView(
            $this->view(['message' => 'Your account has been successfully changed'], Response::HTTP_OK)
        );
    }
}