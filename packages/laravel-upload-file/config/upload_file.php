<?php

return [

    /*
 * List of adapters to use for various source inputs
 *
 * Adapters can map either to a class or a pattern (regex)
 */
    'source_adapters' => [
        'class' => [
            Symfony\Component\HttpFoundation\File\UploadedFile::class => Udhuong\LaravelUploadFile\SourceAdapters\UploadedFileAdapter::class,
            Symfony\Component\HttpFoundation\File\File::class => Udhuong\LaravelUploadFile\SourceAdapters\FileAdapter::class,
        ],
        'pattern' => [
            '^https?://' => Udhuong\LaravelUploadFile\SourceAdapters\RemoteUrlAdapter::class,
            '^/' => Udhuong\LaravelUploadFile\SourceAdapters\LocalPathAdapter::class,
            '^[a-zA-Z]:\\\\' => Udhuong\LaravelUploadFile\SourceAdapters\LocalPathAdapter::class
        ],
    ],

    /*
     * List of URL Generators to use for handling various filesystem drivers
     *
     */
    'url_generators' => [
        'local' => Udhuong\LaravelUploadFile\UrlGenerators\LocalUrlGenerator::class,
        's3' => Udhuong\LaravelUploadFile\UrlGenerators\S3UrlGenerator::class,
    ],
];
