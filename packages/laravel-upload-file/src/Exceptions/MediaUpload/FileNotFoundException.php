<?php
declare(strict_types=1);

namespace Udhuong\LaravelUploadFile\Exceptions\MediaUpload;

use Udhuong\LaravelUploadFile\Exceptions\MediaUploadException;

class FileNotFoundException extends MediaUploadException
{
    public static function fileNotFound(string $path): self
    {
        return new static("File `{$path}` does not exist.");
    }
}
