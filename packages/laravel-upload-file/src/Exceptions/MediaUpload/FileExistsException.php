<?php
declare(strict_types=1);

namespace Udhuong\LaravelUploadFile\Exceptions\MediaUpload;

use Udhuong\LaravelUploadFile\Exceptions\MediaUploadException;

class FileExistsException extends MediaUploadException
{
    public static function fileExists(string $path): self
    {
        return new static("A file already exists at `{$path}`.");
    }
}
