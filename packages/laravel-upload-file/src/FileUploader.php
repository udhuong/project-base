<?php
declare(strict_types=1);

namespace Udhuong\LaravelUploadFile;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use stdClass;
use Udhuong\LaravelUploadFile\Exceptions\MediaUpload\ConfigurationException;
use Udhuong\LaravelUploadFile\Exceptions\MediaUpload\FileExistsException;
use Udhuong\LaravelUploadFile\Exceptions\MediaUpload\FileNotFoundException;
use Udhuong\LaravelUploadFile\Exceptions\MediaUpload\FileNotSupportedException;
use Udhuong\LaravelUploadFile\Exceptions\MediaUpload\FileSizeException;
use Udhuong\LaravelUploadFile\Exceptions\MediaUpload\ForbiddenException;
use Udhuong\LaravelUploadFile\Helpers\File;
use Udhuong\LaravelUploadFile\SourceAdapters\RawContentAdapter;
use Udhuong\LaravelUploadFile\SourceAdapters\SourceAdapterFactory;
use Udhuong\LaravelUploadFile\Traits\HasDuplicate;
use Udhuong\LaravelUploadFile\Traits\HasFileName;
use Udhuong\LaravelUploadFile\Traits\HasStrictType;
use Udhuong\LaravelUploadFile\Traits\HasVerify;
use Udhuong\LaravelUploadFile\UrlGenerators\UrlGeneratorInterface;

class FileUploader
{
    use HasDuplicate;
    use HasVerify;
    use HasFileName;
    use HasStrictType;

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
    private $config;

    /**
     * Source adapter.
     * @var \Udhuong\LaravelUploadFile\SourceAdapters\SourceAdapterInterface
     */
    private $source;

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
     * Visibility for the new file
     * @var string
     */
    private string $visibility = Filesystem::VISIBILITY_PUBLIC;

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
     * @param mixed $source
     *
     * @return $this
     * @throws ConfigurationException
     */
    public function fromSource(mixed $source): self
    {
        $this->source = $this->factory->create($source);

        return $this;
    }

    /**
     * Set the source for the string data.
     * @param string $source
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
     * @param string $disk
     * @param string $directory
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

    /**
     * Process the file upload.
     *
     * Validates the source, then stores the file onto the disk and creates and stores a new Media instance.
     *
     * @return array
     * @throws ConfigurationException
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws FileNotSupportedException
     * @throws FileSizeException
     */
    public function upload(): array
    {
        $this->verifyFile();
        $file = new stdClass;
        $file = $this->populateFile($file);
        $this->verifyDestination($file);
        $this->writeToDisk($file);
        $file->path = $this->getDiskPath($file);

        $urlGenerator = $this->getUrlGenerator($file);
        $file->absolute_path = $urlGenerator->getAbsolutePath();
        $file->url = $urlGenerator->getUrl();
        return (array)$file;
    }

    private function writeToDisk($file): void
    {
        $stream = $this->source->getStreamResource();

        if (!is_resource($stream)) {
            $stream = $this->source->contents();
        }
        $this->filesystem->disk($file->disk)
            ->put(
                $this->getDiskPath($file),
                $stream,
                $this->getOptions()
            );

        if (is_resource($stream)) {
            fclose($stream);
        }
    }

    private function getDiskPath($file)
    {
        $basename = $file->filename . '.' . $file->extension;
        return ltrim(File::joinPathComponents((string)$this->directory, (string)$basename), '/');
    }

    /**
     * Validate input and convert to Media attributes
     * @return object
     *
     * @throws ConfigurationException
     * @throws FileNotFoundException
     * @throws FileNotSupportedException
     * @throws FileSizeException
     */
    private function populateFile($model)
    {
        $model->size = $this->verifyFileSize($this->source->size());
        $model->mime_type = $this->verifyMimeType($this->source->mimeType());
        $model->extension = $this->verifyExtension($this->source->extension());
        $model->aggregate_type = $this->inferAggregateType($model->mime_type, $model->extension);

        $model->disk = $this->disk ?: $this->config['default_disk'];
        $model->directory = $this->directory;
        $model->filename = $this->generateFilename();

        return $model;
    }

    /**
     * Get a UrlGenerator instance for the media.
     * @return UrlGeneratorInterface
     */
    protected function getUrlGenerator($file): UrlGeneratorInterface
    {
        return app('upload_file.url.factory')->create($file);
    }

    /**
     * Make the resulting file public (default behaviour)
     * @return $this
     */
    public function makePublic(): self
    {
        $this->visibility = Filesystem::VISIBILITY_PUBLIC;
        return $this;
    }

    /**
     * Make the resulting file private
     * @return $this
     */
    public function makePrivate(): self
    {
        $this->visibility = Filesystem::VISIBILITY_PRIVATE;
        return $this;
    }

    /**
     * Additional options to pass to the filesystem when uploading
     * @param array $options
     * @return $this
     */
    public function withOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions(): array
    {
        $options = $this->options;
        if (!isset($options['visibility'])) {
            $options['visibility'] = $this->visibility;
        }
        return $options;
    }
}
