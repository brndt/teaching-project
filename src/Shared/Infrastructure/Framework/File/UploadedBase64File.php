<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\File;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadedBase64File extends UploadedFile
{
    public function __construct(string $base64String, string $originalName)
    {
        $base64AsArray = explode(';base64,', $base64String);
        $filePath = tempnam(sys_get_temp_dir(), 'UploadedFile');

        if (2 !== count($base64AsArray)) {
            throw new InvalidArgumentException(sprintf('Invalid image'));
        }

        $data = base64_decode($base64AsArray[1]);
        file_put_contents($filePath, $data);
        $error = null;
        $mimeType = null;
        $test = true;

        parent::__construct($filePath, $originalName, $mimeType, $error, $test);
    }

}

