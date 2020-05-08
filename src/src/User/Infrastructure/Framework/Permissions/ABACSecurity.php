<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\User\Infrastructure\Framework\Permissions;

use LaSalle\StudentTeacher\User\Domain\CheckPermission;
use PhpAbac\AbacFactory;
use Symfony\Component\Finder\Finder;

final class ABACSecurity implements CheckPermission
{

    public function isGranted($rule, $user, $resource): bool
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__ . '/Rules');
        if ($finder->hasResults()) {
            $filesWithRules = [];
            foreach ($finder as $file) {
                $filesWithRules[] = $file->getRealPath();
            }
            $abac = AbacFactory::getAbac($filesWithRules);
            return $abac->enforce($rule, $user, $resource);
        }
        return false;
    }
}