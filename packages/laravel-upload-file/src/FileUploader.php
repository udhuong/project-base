<?php
declare(strict_types=1);

namespace Udhuong\LaravelUploadFile;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Udhuong\LaravelUploadFile\Exceptions\MediaUpload\ConfigurationException;
use Udhuong\LaravelUploadFile\Exceptions\MediaUpload\ForbiddenException;
use Udhuong\LaravelUploadFile\Helpers\File;
use Udhuong\LaravelUploadFile\SourceAdapters\RawContentAdapter;
use Udhuong\LaravelUploadFile\SourceAdapters\SourceAdapterFactory;
use Udhuong\LaravelUploadFile\Traits\HasDuplicate;

class FileUploader
{
    use HasDuplicate;

    const ON_DUPLICATE_UPDATE = 'update';
    const ON_DUPLICATE_INCREMENT = 'increment';
    const ON_DUPLICATE_ERROR = 'error';
    const ON_DUPLICATE_REPLACE = 'replace';

    /**
     * @var FileSystemManager
     */
    private FilesystemManager $filesystem;

    /**
     * @var SourceAdapterFactory
     */
    private $factory;

    /**
     * Configurations.
     * @var array
     */
    private array $config;

    /**
     * Name of the filesystem disk.
     * @var string
     */
    private string $disk;

    /**
     * Path relative to the filesystem disk root.
     * @var string
     */
    private string $directory = '';

    /**
     * Name of the new file.
     * @var string|null
     */
    private ?string $filename = null;

    /**
     * If true the contents hash of the source will be used as the filename.
     * @var bool
     */
    private bool $hashFilename = false;

    /**
     * Additional options to pass to the filesystem while uploading
     * @var array
     */
    private array $options = [];

    /**
     * Constructor.
     * @param FilesystemManager $filesystem
     * @param SourceAdapterFactory $factory
     * @param array|null $config
     */
    public function __construct(FileSystemManager $filesystem, SourceAdapterFactory $factory, array $config = null)
    {
        $this->filesystem = $filesystem;
        $this->factory = $factory;
        $this->config = $config ?: config('upload_file', []);
    }

    /**
     * Set the source for the file.
     *
     * @param  mixed $source
     *
     * @return $this
     * @throws ConfigurationException
     */
    public function fromSource($source): self
    {
        $this->source = $this->factory->create($source);

        return $this;
    }

    /**
     * Set the source for the string data.
     * @param  string $source
     * @return $this
     */
    public function fromString(string $source): self
    {
        $this->source = new RawContentAdapter($source);

        return $this;
    }

    /**
     * Set the filesystem disk and relative directory where the file will be saved.
     *
     * @param  string $disk
     * @param  string $directory
     *
     * @return $this
     * @throws ConfigurationException
     * @throws ForbiddenException
     */
    public function toDestination(string $disk, string $directory): self
    {
        return $this->toDisk($disk)->toDirectory($directory);
    }

    /**
     * Set the filesystem disk on which the file will be saved.
     *
     * @param string $disk
     *
     * @return $this
     * @throws ConfigurationException
     * @throws ForbiddenException
     */
    public function toDisk(string $disk): self
    {
        $this->disk = $this->verifyDisk($disk);

        return $this;
    }

    /**
     * Set the directory relative to the filesystem disk at which the file will be saved.
     * @param string $directory
     * @return $this
     */
    public function toDirectory(string $directory): self
    {
        $this->directory = File::sanitizePath($directory);

        return $this;
    }
}
