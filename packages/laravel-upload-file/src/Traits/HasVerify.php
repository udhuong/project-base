<?php

namespace Udhuong\LaravelUploadFile\Traits;

use Udhuong\LaravelUploadFile\Exceptions\MediaUpload\ConfigurationException;
use Udhuong\LaravelUploadFile\Exceptions\MediaUpload\FileExistsException;
use Udhuong\LaravelUploadFile\Exceptions\MediaUpload\FileNotFoundException;
use Udhuong\LaravelUploadFile\Exceptions\MediaUpload\FileNotSupportedException;
use Udhuong\LaravelUploadFile\Exceptions\MediaUpload\FileSizeException;
use Udhuong\LaravelUploadFile\Exceptions\MediaUpload\ForbiddenException;

trait HasVerify
{
    /**
     * Ensure that the provided filesystem disk name exists and is allowed.
     * @param  string $disk
     * @return string
     * @throws ConfigurationException If the disk does not exist
     * @throws ForbiddenException If the disk is not included in the `allowed_disks` config.
     */
    private function verifyDisk(string $disk): string
    {
        if (!array_key_exists($disk, config('filesystems.disks', []))) {
            throw ConfigurationException::diskNotFound($disk);
        }

        if (!in_array($disk, $this->config['allowed_disks'] ?? [])) {
            throw ForbiddenException::diskNotAllowed($disk);
        }

        return $disk;
    }

    /**
     * Verify if file is valid
     * @throws ConfigurationException If no source is provided
     * @throws FileNotFoundException If the source is invalid
     * @throws FileSizeException If the file is too large
     * @throws FileNotSupportedException If the mime type is not allowed
     * @throws FileNotSupportedException If the file extension is not allowed
     * @return void
     */
    public function verifyFile(): void
    {
        $this->verifySource();
        $this->verifyFileSize($this->source->size());
        $this->verifyMimeType($this->source->mimeType());
        $this->verifyExtension($this->source->extension());
    }

    /**
     * Ensure that a valid source has been provided.
     * @return void
     * @throws ConfigurationException If no source is provided
     * @throws FileNotFoundException If the source is invalid
     */
    private function verifySource(): void
    {
        if (empty($this->source)) {
            throw ConfigurationException::noSourceProvided();
        }
        if (!$this->source->valid()) {
            throw FileNotFoundException::fileNotFound($this->source->path());
        }
    }

    /**
     * Ensure that the file's mime type is allowed.
     * @param  string $mimeType
     * @return string
     * @throws FileNotSupportedException If the mime type is not allowed
     */
    private function verifyMimeType(string $mimeType): string
    {
        $mimeType = strtolower($mimeType);
        $allowed = $this->config['allowed_mime_types'] ?? [];
        if (!empty($allowed) && !in_array($mimeType, $allowed)) {
            throw FileNotSupportedException::mimeRestricted($mimeType, $allowed);
        }

        return $mimeType;
    }

    /**
     * Ensure that the file's extension is allowed.
     * @param  string $extension
     * @return string
     * @throws FileNotSupportedException If the file extension is not allowed
     */
    private function verifyExtension(string $extension, bool $toLower = true): string
    {
        $extensionLower = strtolower($extension);
        $allowed = $this->config['allowed_extensions'] ?? [];
        if (!empty($allowed) && !in_array($extensionLower, $allowed)) {
            throw FileNotSupportedException::extensionRestricted($extensionLower, $allowed);
        }

        return $toLower ? $extensionLower : $extension;
    }

    /**
     * Verify that the file being uploaded is not larger than the maximum.
     * @param  int $size
     * @return int
     * @throws FileSizeException If the file is too large
     */
    private function verifyFileSize(int $size): int
    {
        $max = $this->config['max_size'] ?? 0;
        if ($max > 0 && $size > $max) {
            throw FileSizeException::fileIsTooBig($size, $max);
        }

        return $size;
    }

    /**
     * Verify that the intended destination is available and handle any duplications.
     * @return void
     *
     * @throws FileExistsException
     */
    private function verifyDestination($file): void
    {
        $storage = $this->filesystem->disk($file->disk);
        if ($storage->has($this->getDiskPath($file))) {
            $this->handleDuplicate($file);
        }
    }

    /**
     * Change the maximum allowed file size.
     * @param int $size
     * @return $this
     */
    public function setMaximumSize(int $size): self
    {
        $this->config['max_size'] = (int)$size;

        return $this;
    }
}
