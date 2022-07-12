<?php
defined('TYPE_IMAGE') || define('TYPE_IMAGE', 'image');
defined('TYPE_IMAGE_VECTOR') || define('TYPE_IMAGE_VECTOR', 'vector');
defined('TYPE_PDF') || define('TYPE_PDF', 'pdf');
defined('TYPE_VIDEO') || define('TYPE_VIDEO', 'video');
defined('TYPE_AUDIO') || define('TYPE_AUDIO', 'audio');
defined('TYPE_ARCHIVE') || define('TYPE_ARCHIVE', 'archive');
defined('TYPE_DOCUMENT') || define('TYPE_DOCUMENT', 'document');
defined('TYPE_SPREADSHEET') || define('TYPE_SPREADSHEET', 'spreadsheet');
defined('TYPE_PRESENTATION') || define('TYPE_PRESENTATION', 'presentation');
defined('TYPE_OTHER') || define('TYPE_OTHER', 'other');
defined('TYPE_ALL') || define('TYPE_ALL', 'all');

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
     * List of aggregate types recognized by the application
     *
     * Each type should list the MIME types and extensions
     * that should be recognized for the type
     */
    'aggregate_types' => [
        TYPE_IMAGE => [
            'mime_types' => [
                'image/jpeg',
                'image/png',
                'image/gif',
            ],
            'extensions' => [
                'jpg',
                'jpeg',
                'png',
                'gif',
            ],
        ],
        TYPE_IMAGE_VECTOR => [
            'mime_types' => [
                'image/svg+xml',
            ],
            'extensions' => [
                'svg',
            ],
        ],
        TYPE_PDF => [
            'mime_types' => [
                'application/pdf',
            ],
            'extensions' => [
                'pdf',
            ],
        ],
        TYPE_AUDIO => [
            'mime_types' => [
                'audio/aac',
                'audio/ogg',
                'audio/mpeg',
                'audio/mp3',
                'audio/mpeg',
                'audio/wav',
            ],
            'extensions' => [
                'aac',
                'ogg',
                'oga',
                'mp3',
                'wav',
            ],
        ],
        TYPE_VIDEO => [
            'mime_types' => [
                'video/mp4',
                'video/mpeg',
                'video/ogg',
                'video/webm',
            ],
            'extensions' => [
                'mp4',
                'm4v',
                'mov',
                'ogv',
                'webm',
            ],
        ],
        TYPE_ARCHIVE => [
            'mime_types' => [
                'application/zip',
                'application/x-compressed-zip',
                'multipart/x-zip',
            ],
            'extensions' => [
                'zip',
            ],
        ],
        TYPE_DOCUMENT => [
            'mime_types' => [
                'text/plain',
                'application/plain',
                'text/xml',
                'text/json',
                'application/json',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],
            'extensions' => [
                'doc',
                'docx',
                'txt',
                'text',
                'xml',
                'json',
            ],
        ],
        TYPE_SPREADSHEET => [
            'mime_types' => [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ],
            'extensions' => [
                'xls',
                'xlsx',
            ],
        ],
        TYPE_PRESENTATION => [
            'mime_types' =>
                [
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
                ],
            'extensions' =>
                [
                    'ppt',
                    'pptx',
                    'ppsx',
                ],
        ],
    ],

    /*
     * List of adapters to use for various source inputs
     *
     * Adapters can map either to a class or a pattern (regex)
     */
    'source_adapters' => [
        'class' => [
            Symfony\Component\HttpFoundation\File\UploadedFile::class => Udhuong\LaravelUploadFile\SourceAdapters\UploadedFileAdapter::class,
            Symfony\Component\HttpFoundation\File\File::class => Udhuong\LaravelUploadFile\SourceAdapters\FileAdapter::class,
            Psr\Http\Message\StreamInterface::class => Udhuong\LaravelUploadFile\SourceAdapters\StreamAdapter::class,
        ],
        'pattern' => [
            '^https?://' => Udhuong\LaravelUploadFile\SourceAdapters\RemoteUrlAdapter::class,
            '^/' => Udhuong\LaravelUploadFile\SourceAdapters\LocalPathAdapter::class,
            '^[a-zA-Z]:\\\\' => Udhuong\LaravelUploadFile\SourceAdapters\LocalPathAdapter::class,
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
