<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\User\Application\BasicUserInformation\Update\UpdateBasicUserInformation;
use LaSalle\StudentTeacher\User\Application\BasicUserInformation\Update\UpdateBasicUserInformationRequest;
use LaSalle\StudentTeacher\User\Application\User\Search\SearchUserByEmail;
use LaSalle\StudentTeacher\User\Application\User\Search\SearchUserByEmailRequest;
use LaSalle\StudentTeacher\User\Application\User\Search\SearchUserById;
use LaSalle\StudentTeacher\User\Application\User\Search\SearchUserByIdRequest;
use Symfony\Component\HttpFoundation\Response;

final class UpdateBasicUserInformationController extends AbstractFOSRestController
{
    private UpdateBasicUserInformation $updateUser;
    private SearchUserByEmail $searchUser;
    private SearchUserByEmail $searchUserByEmail;
    private SearchUserById $searchUserById;

    public function __construct(UpdateBasicUserInformation $updateUser, SearchUserByEmail $searchUserByEmail, SearchUserById $searchUserById)
    {
        $this->updateUser = $updateUser;
        $this->searchUserByEmail = $searchUserByEmail;
        $this->searchUserById = $searchUserById;
    }

    /**
     * @Rest\Patch("/api/account/{id}")
     * @RequestParam(name="username")
     * @RequestParam(name="firstName")
     * @RequestParam(name="lastName")
     * @RequestParam(name="image")
     * @RequestParam(name="education")
     * @RequestParam(name="experience")
     */
    public function patchAction(ParamFetcher $paramFetcher, int $id)
    {
        $email = $paramFetcher->get('username');
        $firstName = $paramFetcher->get('firstName');
        $lastName = $paramFetcher->get('lastName');
        $image = $paramFetcher->get('image');
        $education = $paramFetcher->get('education');
        $experience = $paramFetcher->get('experience');

        if ($id !== $this->getUser()->getId()) {
            $view = $this->view(
                ['message' => 'You don\'t have permission to update this profile'],
                Response::HTTP_FORBIDDEN
            );
            return $this->handleView($view);
        }

        $newEmailIsExist = ($this->searchUserByEmail)(new SearchUserByEmailRequest($email));
        $userByOldEmail = ($this->searchUserById)(new SearchUserByIdRequest($id));

        if (null !== $newEmailIsExist && $email !== $userByOldEmail->getEmail()) {
            $view = $this->view(
                ['message' => 'Your new email is already registered'],
                Response::HTTP_BAD_REQUEST
            );
            return $this->handleView($view);
        }

        ($this->updateUser)(
            new UpdateBasicUserInformationRequest(
                $id, $email, $firstName, $lastName, $education, $experience, $image
            )
        );

        $view = $this->view(['message' => 'Your account has been successfully changed'], Response::HTTP_OK);
        return $this->handleView($view);
    }
}