<?php
declare(strict_types=1);

namespace Udhuong\LaravelUploadFile\SourceAdapters;

use Udhuong\LaravelUploadFile\Exceptions\MediaUpload\ConfigurationException;
use Udhuong\LaravelUploadFile\Stream;

/**
 * Stream resource Adapter.
 *
 * Adapts a stream resource.
 */
class StreamResourceAdapter extends StreamAdapter
{
    /**
     * The resource.
     * @var resource|null
     */
    protected $resource;

    /**
     * Constructor.
     * @param resource $source
     * @throws ConfigurationException
     */
    public function __construct($source)
    {
        if (!is_resource($source) || get_resource_type($source) !== 'stream') {
            throw ConfigurationException::unrecognizedSource($source);
        }

        parent::__construct(new Stream($source));

        $this->resource = $source;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->resource;
    }

    /**
     * @inheritdoc
     */
    public function getStreamResource()
    {
        return $this->resource;
    }
}
