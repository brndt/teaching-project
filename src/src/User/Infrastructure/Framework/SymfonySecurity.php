<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework;

use LaSalle\StudentTeacher\User\Domain\CheckPermission;
use Symfony\Component\Security\Core\Security;

final class SymfonySecurity implements CheckPermission
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function isGranted($attributes, $subject)
    {
        return $this->security->isGranted($attributes, $subject);
    }
}