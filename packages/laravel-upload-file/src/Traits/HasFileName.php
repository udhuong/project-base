<?php

namespace Udhuong\LaravelUploadFile\Traits;

use Udhuong\LaravelUploadFile\Helpers\File;

trait HasFileName
{
    /**
     * Specify the filename to copy to the file to.
     * @param string $filename
     * @return $this
     */
    public function useFilename(string $filename): self
    {
        $this->filename = File::sanitizeFilename($filename);
        $this->hashFilename = false;

        return $this;
    }

    /**
     * Indicates to the uploader to generate a filename using the file's MD5 hash.
     * @return $this
     */
    public function useHashForFilename(): self
    {
        $this->hashFilename = true;
        $this->filename = null;

        return $this;
    }

    /**
     * Restore the default behaviour of using the source file's filename.
     * @return $this
     */
    public function useOriginalFilename(): self
    {
        $this->filename = null;
        $this->hashFilename = false;

        return $this;
    }

    /**
     * Increment filename until one is found that doesn't already exist.
     * @return string
     */
    private function generateUniqueFilename($file): string
    {
        $storage = $this->filesystem->disk($file->disk);
        $counter = 0;
        do {
            $filename = "{$file->filename}";
            if ($counter > 0) {
                $filename .= '-' . $counter;
            }
            $path = "{$file->directory}/{$filename}.{$file->extension}";
            ++$counter;
        } while ($storage->has($path));
        return $filename;
    }

    /**
     * Generate the model's filename.
     * @return string
     */
    private function generateFilename(): string
    {
        if ($this->filename) {
            return $this->filename;
        }
        if ($this->hashFilename) {
            return $this->generateHash();
        }
        return File::sanitizeFileName($this->source->filename());
    }

    /**
     * Calculate hash of source contents.
     * @return string
     */
    private function generateHash(): string
    {
        $ctx = hash_init('md5');

        // We don't need to read the file contents if the source has a path
        if ($this->source->path()) {
            hash_update_file($ctx, $this->source->path());
        } else {
            hash_update($ctx, $this->source->contents());
        }

        return hash_final($ctx);
    }
}
