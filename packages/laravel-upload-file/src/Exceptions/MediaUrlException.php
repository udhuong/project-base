<?php
declare(strict_types=1);

namespace Udhuong\LaravelUploadFile\Exceptions;

use Exception;

class MediaUrlException extends Exception
{
    public static function generatorNotFound(string $disk, string $driver): self
    {
        return new static("Could not find UrlGenerators for disk `{$disk}` of type `{$driver}`");
    }

    public static function invalidGenerator(string $class): self
    {
        return new static("Could not set UrlGenerators, class `{$class}` does not extend `Udhuong\LaravelUploadFile\Exceptions\UrlGenerators`");
    }

    public static function temporaryUrlsNotSupported(string $disk): self
    {
        return new static("Temporary URLs are not supported for files on disk '{$disk}'");
    }
}
