<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

final class LoginController extends AbstractFOSRestController
{
    /**
     * @Rest\Post("/api/sign_in", name="sign_in")
     */
    public function signIn()
    {
    }
}