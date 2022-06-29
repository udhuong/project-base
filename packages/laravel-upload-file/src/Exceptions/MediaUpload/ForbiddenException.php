<?php
declare(strict_types=1);

namespace Udhuong\LaravelUploadFile\Exceptions\MediaUpload;

use Udhuong\LaravelUploadFile\Exceptions\MediaUploadException;

class ForbiddenException extends MediaUploadException
{
    public static function diskNotAllowed(string $disk): self
    {
        return new static("The disk `{$disk}` is not in the allowed disks for media.");
    }
}
