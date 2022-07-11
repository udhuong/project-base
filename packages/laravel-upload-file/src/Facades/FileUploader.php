<?php

namespace Udhuong\LaravelUploadFile\Facades;
use Illuminate\Support\Facades\Facade;
use Udhuong\LaravelUploadFile\FileUploader as Uploader;
/**
 * Facade for Media Uploader.
 *
 * @method static Uploader fromSource(mixed $source)
 * @method static Uploader fromString(string $source)
 * @method static Uploader toDestination(string $disk, string $directory)
 * @method static Uploader toDisk(string $disk)
 * @method static Uploader toDirectory(string $directory)
 * @method static Uploader onDuplicateError()
 * @method static Uploader onDuplicateIncrement()
 * @method static Uploader onDuplicateReplace()
 */
class FileUploader extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'upload_file.uploader';
    }

    public static function getFacadeRoot()
    {
        // prevent the facade from behaving like a singleton
        if (!self::isMock()) {
            self::clearResolvedInstance('upload_file.uploader');
        }
        return parent::getFacadeRoot();
    }
}
