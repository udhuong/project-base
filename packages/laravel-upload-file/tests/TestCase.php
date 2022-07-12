<?php

namespace Udhuong\LaravelUploadFile\Tests;

use Dotenv\Dotenv;
use Faker\Factory;
use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Udhuong\LaravelUploadFile\FileUploader;
use Udhuong\LaravelUploadFile\UploadFileServiceProvider;

class TestCase extends BaseTestCase
{
    const TEST_FILE_SIZE = 7173;

    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            UploadFileServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'FileUploader' => \Udhuong\LaravelUploadFile\Facades\FileUploader::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        if (file_exists(dirname(__DIR__) . '/.env')) {
            Dotenv::create(dirname(__DIR__))->load();
        }
        //use in-memory database
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => ''
        ]);
        $app['config']->set('database.default', 'testing');
        $app['config']->set('filesystems.default', 'uploads');
        $app['config']->set('filesystems.disks', [
            //private local storage
            'tmp' => [
                'driver' => 'local',
                'root' => storage_path('tmp'),
                'visibility' => 'private'
            ],
            //public local storage
            'uploads' => [
                'driver' => 'local',
                'root' => public_path('uploads'),
                'url' => 'http://localhost/uploads',
                'visibility' => 'public'
            ],
            'public_storage' => [
                'driver' => 'local',
                'root' => storage_path('public'),
                'url' => 'http://localhost/storage',
                'visibility' => 'public',
            ],
            's3' => [
                'driver' => 's3',
                'key' => env('S3_KEY'),
                'secret' => env('S3_SECRET'),
                'region' => env('S3_REGION'),
                'bucket' => env('S3_BUCKET'),
                'version' => 'latest',
                'visibility' => 'public',
                // set random root to avoid parallel test runs from deleting each other's files
                'root' => Factory::create()->md5
            ]
        ]);

        $app['config']->set('upload_file.allowed_disks', [
            'tmp',
            'uploads'
        ]);
    }

    protected function s3ConfigLoaded()
    {
        return env('S3_KEY') && env('S3_SECRET') && env('S3_REGION') && env('S3_BUCKET');
    }

    protected function useFilesystem($disk)
    {
        if (!$this->app['config']->has('filesystems.disks.' . $disk)) {
            return;
        }
        $root = $this->app['config']->get('filesystems.disks.' . $disk . '.root');
        $filesystem = $this->app->make(Filesystem::class);
        $filesystem->cleanDirectory($root);
    }

    protected function useFilesystems()
    {
        $disks = $this->app['config']->get('filesystems.disks');
        foreach ($disks as $disk) {
            $this->useFilesystem($disk);
        }
    }

    protected function sampleFilePath()
    {
        return realpath(__DIR__ . '/_data/plank.png');
    }

    protected function alternateFilePath()
    {
        return realpath(__DIR__ . '/_data/plank2.png');
    }

    protected function remoteFilePath()
    {
        return 'https://raw.githubusercontent.com/udhuong/project-base/master/packages/laravel-upload-file/tests/_data/plank.png';
    }

    protected function sampleFile()
    {
        return fopen($this->sampleFilePath(), 'r');
    }

    /**
     * Check if the file exists on disk.
     * @return bool
     */
    protected function checkFileExists(string $disk,string $path): bool
    {
        return app('filesystem')->disk($disk)->has($path);
    }

    protected function getUploader(): FileUploader
    {
        return app('upload_file.uploader');
    }
}
