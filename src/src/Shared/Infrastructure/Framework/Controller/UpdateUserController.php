<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserInformationRequest;
use LaSalle\StudentTeacher\User\Application\Service\UpdateUserInformation;
use Symfony\Component\HttpFoundation\Response;

final class UpdateUserController extends AbstractFOSRestController
{
    private UpdateUserInformation $updateUserInformation;

    public function __construct(UpdateUserInformation $updateUser)
    {
        $this->updateUserInformation = $updateUser;
    }

    /**
     * @Rest\Patch("/api/v1/users/{userId}/info")
     * @RequestParam(name="username")
     * @RequestParam(name="firstName")
     * @RequestParam(name="lastName")
     * @RequestParam(name="image")
     * @RequestParam(name="education")
     * @RequestParam(name="experience")
     */
    public function patchAction(ParamFetcher $paramFetcher, string $userId): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $email = $paramFetcher->get('username');
        $firstName = $paramFetcher->get('firstName');
        $lastName = $paramFetcher->get('lastName');
        $image = $paramFetcher->get('image');
        $education = $paramFetcher->get('education');
        $experience = $paramFetcher->get('experience');

        ($this->updateUserInformation)(
            new UpdateUserInformationRequest($requestAuthorId, $userId, $email, $firstName, $lastName, $image, $experience, $education)
        );

        return $this->handleView(
            $this->view(['message' => 'Your account has been successfully changed'], Response::HTTP_OK)
        );
    }
}