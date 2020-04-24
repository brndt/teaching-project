<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\User\Application\Exception\OldPasswordIncorrectException;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
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
    public function patchAction(ParamFetcher $paramFetcher, string $id)
    {
        $oldPassword = $paramFetcher->get('oldPassword');
        $newPassword = $paramFetcher->get('newPassword');

        if ($id !== $this->getUser()->getId()) {
            $view = $this->view(
                ['message' => 'You don\'t have permission to update this profile'],
                Response::HTTP_FORBIDDEN
            );
            return $this->handleView($view);
        }

        try {
            ($this->updatePassword)(new UpdateUserPasswordRequest($id, $oldPassword, $newPassword));
        } catch (UserNotFoundException $e) {
            $view = $this->view(['message' => 'There\'s no user with such id'], Response::HTTP_NOT_FOUND);
            return $this->handleView($view);
        } catch (OldPasswordIncorrectException $e) {
            $view = $this->view(['message' => 'Your old password was entered incorrectly'], Response::HTTP_BAD_REQUEST);
            return $this->handleView($view);
        } catch (InvalidArgumentValidationException $error) {
            $view = $this->view(
                ['message' => $error->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
            return $this->handleView($view);
        }

        $view = $this->view(['message' => 'Your account has been successfully changed'], Response::HTTP_OK);
        return $this->handleView($view);
    }
}