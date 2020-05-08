<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Domain;

interface CheckPermission
{
    public function isGranted($rule, $user, $resource): bool;
}