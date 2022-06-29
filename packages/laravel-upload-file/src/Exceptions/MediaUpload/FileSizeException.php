<?php
declare(strict_types=1);

namespace Udhuong\LaravelUploadFile\Exceptions\MediaUpload;

use Udhuong\LaravelUploadFile\Exceptions\MediaUploadException;

class FileSizeException extends MediaUploadException
{
    public static function fileIsTooBig(int $size, int $max): self
    {
        return new static("File is too big ({$size} bytes). Maximum upload size is {$max} bytes.");
    }
}
