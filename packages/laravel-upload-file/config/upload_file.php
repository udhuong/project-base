<?php

return [
    /*
 * Filesystem disk to use if none is specified
 */
    'default_disk' => 'public',

    /*
     * Filesystems that can be used for media storage
     *
     * Uploader will throw an exception if a disk not in this list is selected
     */
    'allowed_disks' => [
        'public',
        's3',
    ],

    /*
    * The maximum file size in bytes for a single uploaded file
    */
    'max_size' => 1024 * 1024 * 10,

    /*
    * What to do if a duplicate file is uploaded.
    *
    * Options include:
    *
    * * `'increment'`: the new file's name is given an incrementing suffix
    * * `'replace'` : the old file and media model is deleted
    * * `'error'`: an Exception is thrown
    */
    'on_duplicate' => Udhuong\LaravelUploadFile\FileUploader::ON_DUPLICATE_INCREMENT,

    /*
      * Reject files unless both their mime and extension are recognized and both match a single aggregate type
      */
    'strict_type_checking' => false,

    /*
     * Reject files whose mime type or extension is not recognized
     * if true, files will be given a type of `'other'`
     */
    'allow_unrecognized_types' => false,

    /*
     * Only allow files with specific MIME type(s) to be uploaded
     */
    'allowed_mime_types' => [],

    /*
     * Only allow files with specific file extension(s) to be uploaded
     */
    'allowed_extensions' => [],

    /*
     * Only allow files matching specific aggregate type(s) to be uploaded
     */
    'allowed_aggregate_types' => [],

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
