<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure;

use LaSalle\StudentTeacher\Shared\Domain\RandomStringGenerator;

final class RandomStringFromBytesGenerator implements RandomStringGenerator
{
    public function generate(): string
    {
        return bin2hex(openssl_random_pseudo_bytes(64));
    }
}