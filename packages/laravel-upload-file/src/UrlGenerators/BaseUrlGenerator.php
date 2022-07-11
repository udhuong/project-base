<?php
declare(strict_types=1);

namespace Udhuong\LaravelUploadFile\UrlGenerators;

use Illuminate\Contracts\Config\Repository as Config;
use Udhuong\LaravelUploadFile\Helpers\File;

abstract class BaseUrlGenerator implements UrlGeneratorInterface
{
    /**
     * Configuration Repository.
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Constructor.
     * @param \Illuminate\Contracts\Config\Repository $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Media instance being linked.
     */
    protected $media;

    /**
     * Set the media being operated on.
     */
    public function setMedia($media): void
    {
        $this->media = $media;
    }

    /**
     * {@inheritdoc}
     */
    public function isPubliclyAccessible(): bool
    {
        return $this->getDiskConfig('visibility', 'private') == 'public' && $this->media->isVisible();
    }

    /**
     * Get a config value for the current disk.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getDiskConfig(string $key, $default = null)
    {
        return $this->config->get("filesystems.disks.{$this->media->disk}.{$key}", $default);
    }

    protected function getDiskPath()
    {
        $basename = $this->media->filename . '.' . $this->media->extension;
        return ltrim(File::joinPathComponents((string)$this->media->directory, (string)$basename), '/');
    }
}
