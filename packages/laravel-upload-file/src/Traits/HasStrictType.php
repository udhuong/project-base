<?php

namespace Udhuong\LaravelUploadFile\Traits;

use Udhuong\LaravelUploadFile\Exceptions\MediaUpload\FileNotSupportedException;

trait HasStrictType
{
    /**
     * Change whether both the MIME type and extensions must match the same aggregate type.
     * @param bool $strict
     * @return $this
     */
    public function setStrictTypeChecking(bool $strict): self
    {
        $this->config['strict_type_checking'] = $strict;

        return $this;
    }

    /**
     * Change whether files not matching any aggregate types are allowed.
     * @param bool $allow
     * @return $this
     */
    public function setAllowUnrecognizedTypes(bool $allow): self
    {
        $this->config['allow_unrecognized_types'] = $allow;

        return $this;
    }

    /**
     * Add or update the definition of a aggregate type.
     * @param string $type the name of the type
     * @param string[] $mimeTypes list of MIME types recognized
     * @param string[] $extensions list of file extensions recognized
     * @return $this
     */
    public function setTypeDefinition(string $type, array $mimeTypes, array $extensions): self
    {
        $this->config['aggregate_types'][$type] = [
            'mime_types' => array_map('strtolower', $mimeTypes),
            'extensions' => array_map('strtolower', $extensions),
        ];

        return $this;
    }

    /**
     * Set a list of MIME types that the source file must be restricted to.
     * @param string[] $allowedMimes
     * @return $this
     */
    public function setAllowedMimeTypes(array $allowedMimes): self
    {
        $this->config['allowed_mime_types'] = array_map('strtolower', $allowedMimes);

        return $this;
    }

    /**
     * Set a list of file extensions that the source file must be restricted to.
     * @param string[] $allowedExtensions
     * @return $this
     */
    public function setAllowedExtensions(array $allowedExtensions): self
    {
        $this->config['allowed_extensions'] = array_map('strtolower', $allowedExtensions);

        return $this;
    }

    /**
     * Set a list of aggregate types that the source file must be restricted to.
     * @param string[] $allowedTypes
     * @return $this
     */
    public function setAllowedAggregateTypes(array $allowedTypes): self
    {
        $this->config['allowed_aggregate_types'] = $allowedTypes;

        return $this;
    }

    /**
     * Determine the aggregate type of the file based on the MIME type and the extension.
     * @param  string $mimeType
     * @param  string $extension
     * @return string
     * @throws FileNotSupportedException If the file type is not recognized
     * @throws FileNotSupportedException If the file type is restricted
     * @throws FileNotSupportedException If the aggregate type is restricted
     */
    public function inferAggregateType(string $mimeType, string $extension): string
    {
        $mimeType = strtolower($mimeType);
        $extension = strtolower($extension);
        $allowedTypes = $this->config['allowed_aggregate_types'] ?? [];
        $typesForMime = $this->possibleAggregateTypesForMimeType($mimeType);
        $typesForExtension = $this->possibleAggregateTypesForExtension($extension);

        if (count($allowedTypes)) {
            $intersection = array_intersect($typesForMime, $typesForExtension, $allowedTypes);
        } else {
            $intersection = array_intersect($typesForMime, $typesForExtension);
        }

        if (count($intersection)) {
            $type = $intersection[0];
        } elseif (empty($typesForMime) && empty($typesForExtension)) {
            if (!$this->config['allow_unrecognized_types'] ?? false) {
                throw FileNotSupportedException::unrecognizedFileType($mimeType, $extension);
            }
            $type = TYPE_OTHER;
        } else {
            if ($this->config['strict_type_checking'] ?? false) {
                throw FileNotSupportedException::strictTypeMismatch($mimeType, $extension);
            }
            $merged = array_merge($typesForMime, $typesForExtension);
            $type = reset($merged);
        }

        if (count($allowedTypes) && !in_array($type, $allowedTypes)) {
            throw FileNotSupportedException::aggregateTypeRestricted($type, $allowedTypes);
        }

        return $type;
    }

    /**
     * Determine the aggregate type of the file based on the MIME type.
     * @param  string $mime
     * @return string[]
     */
    public function possibleAggregateTypesForMimeType(string $mime): array
    {
        $types = [];
        foreach ($this->config['aggregate_types'] ?? [] as $type => $attributes) {
            if (in_array($mime, $attributes['mime_types'])) {
                $types[] = $type;
            }
        }

        return $types;
    }

    /**
     * Determine the aggregate type of the file based on the extension.
     * @param  string $extension
     * @return string[]
     */
    public function possibleAggregateTypesForExtension(string $extension): array
    {
        $types = [];
        foreach ($this->config['aggregate_types'] ?? [] as $type => $attributes) {
            if (in_array($extension, $attributes['extensions'])) {
                $types[] = $type;
            }
        }

        return $types;
    }
}
