<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations as Rest;
use LaSalle\StudentTeacher\User\Application\Request\CreateStudentTeacherConnectionRequest;
use LaSalle\StudentTeacher\User\Application\Service\CreateStudentTeacherConnection;
use Symfony\Component\HttpFoundation\Response;

final class CreateStudentTeacherConnectionController extends AbstractFOSRestController
{
    private CreateStudentTeacherConnection $createUserTeacherConnection;

    public function __construct(CreateStudentTeacherConnection $createStudentTeacherConnection)
    {
        $this->createUserTeacherConnection = $createStudentTeacherConnection;
    }

    /**
     * @Rest\Post("/api/v1/connections")
     * @RequestParam(name="pendingId")
     */
    public function postAction(ParamFetcher $paramFetcher): Response
    {
        $pendingId = $paramFetcher->get('pendingId');
        $requestingId = $this->getUser()->getId();

        ($this->createUserTeacherConnection)(new CreateStudentTeacherConnectionRequest($requestingId, $pendingId));

        return $this->handleView(
            $this->view(['message' => 'Request has been successfully sent to this user'], Response::HTTP_OK)
        );
    }
}