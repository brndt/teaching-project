<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserInformationRequest;
use LaSalle\StudentTeacher\User\Application\Service\UpdateUserInformationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UpdateUserInformationController extends AbstractFOSRestController
{
    public function __construct(
        private UpdateUserInformationService $updateUserInformation,
        private ValidatorInterface $validator
    ) {
    }

    /**
     * @Rest\Patch("/api/v1/users/{userId}/info")
     * @RequestParam(name="email")
     * @RequestParam(name="firstName")
     * @RequestParam(name="lastName")
     * @RequestParam(name="education", nullable=true)
     * @RequestParam(name="experience", nullable=true)
     */
    public function patchAction(ParamFetcher $paramFetcher, string $userId): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $email = $paramFetcher->get('email');
        $firstName = $paramFetcher->get('firstName');
        $lastName = $paramFetcher->get('lastName');
        $education = $paramFetcher->get('education');
        $experience = $paramFetcher->get('experience');

        ($this->updateUserInformation)(
            new UpdateUserInformationRequest(
                $requestAuthorId,
                $userId,
                $email,
                $firstName,
                $lastName,
                $experience,
                $education
            )
        );

        return $this->handleView(
            $this->view(['message' => 'Your account has been successfully changed'], Response::HTTP_OK)
        );
    }
}
