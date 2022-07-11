<?php
declare(strict_types=1);

namespace Udhuong\LaravelUploadFile\UrlGenerators;

use Udhuong\LaravelUploadFile\Exceptions\MediaUrlException;

interface UrlGeneratorInterface
{
    /**
     * Retrieve the absolute path to the file.
     *
     * For local files this should return a path
     * For remote files this should return a url
     * @return string
     *
     * @throws MediaUrlException
     */
    public function getAbsolutePath(): string;

    /**
     * Check if the file is publicly accessible.
     *
     * Disks configs should indicate this with the visibility key
     * @return bool
     */
    public function isPubliclyAccessible(): bool;

    /**
     * Get a Url to the file.
     * @return string
     *
     * @throws MediaUrlException
     */
    public function getUrl(): string;
}
