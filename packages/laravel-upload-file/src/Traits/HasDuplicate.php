<?php

namespace Udhuong\LaravelUploadFile\Traits;

use Illuminate\Support\Collection;
use Udhuong\LaravelUploadFile\Exceptions\MediaUpload\FileExistsException;
use Udhuong\LaravelUploadFile\FileUploader;

trait HasDuplicate
{
    /**
     * Change the behaviour for when a file already exists at the destination.
     * @param string $behavior
     * @return $this
     */
    public function setOnDuplicateBehavior(string $behavior): self
    {
        $this->config['on_duplicate'] = (string)$behavior;

        return $this;
    }

    /**
     * Get current behavior when duplicate file is uploaded.
     *
     * @return string
     */
    public function getOnDuplicateBehavior(): string
    {
        return $this->config['on_duplicate'];
    }

    /**
     * Throw an exception when file already exists at the destination.
     *
     * @return $this
     */
    public function onDuplicateError(): self
    {
        return $this->setOnDuplicateBehavior(self::ON_DUPLICATE_ERROR);
    }

    /**
     * Append incremented counter to file name when file already exists at destination.
     *
     * @return $this
     */
    public function onDuplicateIncrement(): self
    {
        return $this->setOnDuplicateBehavior(self::ON_DUPLICATE_INCREMENT);
    }

    /**
     * Overwrite existing Media when file already exists at destination.
     *
     * This will delete the old media record and create a new one, detaching any existing associations.
     *
     * @return $this
     */
    public function onDuplicateReplace(): self
    {
        return $this->setOnDuplicateBehavior(self::ON_DUPLICATE_REPLACE);
    }

    /**
     * Decide what to do about duplicated files.
     *
     * @throws FileExistsException If directory is not writable or file already exists at the destination and on_duplicate is set to 'error'
     */
    private function handleDuplicate($file)
    {
        switch ($this->config['on_duplicate'] ?? FileUploader::ON_DUPLICATE_INCREMENT) {
            case static::ON_DUPLICATE_ERROR:
                throw FileExistsException::fileExists($this->getDiskPath($file));
            case static::ON_DUPLICATE_REPLACE:
                $this->deleteExistingFile($file);
                break;
            case static::ON_DUPLICATE_INCREMENT:
            default:
                $file->filename = $this->generateUniqueFilename($file);
        }
        return $file;
    }

    /**
     * Delete the file on disk.
     * @return void
     */
    private function deleteExistingFile($file): void
    {
        $this->filesystem->disk($file->disk)->delete($this->getDiskPath($file));
    }
}
