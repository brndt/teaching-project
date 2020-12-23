<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Shared\Infrastructure\Framework\File\FileUploader;
use LaSalle\StudentTeacher\Shared\Infrastructure\Framework\File\UploadedBase64File;
use LaSalle\StudentTeacher\User\Application\Request\UpdateUserImageRequest;
use LaSalle\StudentTeacher\User\Application\Service\SearchUserCredentialsByIdService;
use LaSalle\StudentTeacher\User\Application\Service\UpdateUserImageService;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UpdateUserImageController extends AbstractFOSRestController
{
    public function __construct(private UpdateUserImageService $updateUserImage, private ValidatorInterface $validator, private SearchUserCredentialsByIdService $searchUser)
    {
    }

    /**
     * @Rest\Patch("/api/v1/users/{userId}/image")
     * @RequestParam(name="image", nullable=true)
     */
    public function patchAction(ParamFetcher $paramFetcher, string $userId): Response
    {
        $requestAuthorId = $this->getUser()->getId();
        $image = $paramFetcher->get('image');

        if (null !== $image) {
            $newImageAsFile = new UploadedBase64File($image, Uuid::generate()->toString());

            $imageViolations = $this->hasImageViolations($newImageAsFile);
            if (null !== $imageViolations) {
                return $this->handleView(
                    $this->view(['code' => Response::HTTP_BAD_REQUEST, 'message' => $imageViolations], Response::HTTP_BAD_REQUEST)
                );
            }
            $fileUploader = new FileUploader($this->getParameter('imagesDirectory'));
            $image = $fileUploader->upload($newImageAsFile);
        }

        ($this->updateUserImage)(
            new UpdateUserImageRequest($requestAuthorId, $userId, $image)
        );

        return $this->handleView(
            $this->view(['message' => 'Your image has been successfully changed'], Response::HTTP_OK)
        );
    }

    private function hasImageViolations(?File $file): ?string
    {
        $violations = $this->validator->validate(
            $file,
            [new Image(['maxSize' => '2M', 'maxWidth' => 800, 'maxHeight' => 800])]
        );

        if (0 !== count($violations)) {
            $messageViolations = [];
            foreach ($violations as $violation) {
                $messageViolations[] = $violation->getMessage();
            }
            return implode(",", $messageViolations);
        }
        return null;
    }
}
