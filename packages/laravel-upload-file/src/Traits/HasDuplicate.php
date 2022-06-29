<?php

namespace Udhuong\LaravelUploadFile\Traits;

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
     * Overwrite existing files and update the existing media record.
     *
     * This will retain any existing associations.
     *
     * @return $this
     */
    public function onDuplicateUpdate(): self
    {
        return $this->setOnDuplicateBehavior(self::ON_DUPLICATE_UPDATE);
    }
}
