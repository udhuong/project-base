<?php

use Udhuong\LaravelUploadFile\FileUploader;
use Udhuong\LaravelUploadFile\Stream;
use Udhuong\LaravelUploadFile\Tests\TestCase;
use Udhuong\LaravelUploadFile\Facades\FileUploader as Facade;

class FileUploaderTest extends TestCase
{
    public function test_it_can_be_instantiated_via_container()
    {
        $this->assertInstanceOf(\Udhuong\LaravelUploadFile\FileUploader::class, app('upload_file.uploader'));
    }

    public function test_it_can_be_instantiated_via_facade()
    {
        $this->assertInstanceOf(FileUploader::class, Facade::getFacadeRoot());
    }

    public function test_it_can_set_on_duplicate_via_facade()
    {
        $uploader = Facade::onDuplicateError();
        $this->assertEquals(FileUploader::ON_DUPLICATE_ERROR, $uploader->getOnDuplicateBehavior());

        $uploader = Facade::onDuplicateIncrement();
        $this->assertEquals(FileUploader::ON_DUPLICATE_INCREMENT, $uploader->getOnDuplicateBehavior());

        $uploader = Facade::onDuplicateReplace();
        $this->assertEquals(FileUploader::ON_DUPLICATE_REPLACE, $uploader->getOnDuplicateBehavior());
    }

    public function test_can_upload_file_local()
    {
        $this->useFilesystem('tmp');

        $file = Facade::fromSource($this->sampleFilePath())
            ->toDestination('tmp', 'foo')
            ->useFilename('bar')
            ->upload();

        $this->assertIsArray($file);
        $this->assertTrue($this->checkFileExists($file['disk'], $file['path']));
        $this->assertEquals('tmp', $file['disk']);
        $this->assertEquals('foo/bar.png', $file['path']);
        $this->assertEquals('image/png', $file['mime_type']);
        $this->assertEquals(self::TEST_FILE_SIZE, $file['size']);
    }

    public function test_can_upload_file_string_content()
    {
        $this->useFilesystem('tmp');

        $string = file_get_contents($this->sampleFilePath());

        $file = Facade::fromString($string)
            ->toDestination('tmp', 'foo')
            ->useFilename('bar')
            ->upload();

        $this->assertIsArray($file);
        $this->assertTrue($this->checkFileExists($file['disk'], $file['path']));
        $this->assertEquals('tmp', $file['disk']);
        $this->assertEquals('foo/bar.png', $file['path']);
        $this->assertEquals('image/png', $file['mime_type']);
        $this->assertEquals(self::TEST_FILE_SIZE, $file['size']);
    }

    public function test_it_imports_http_stream_contents()
    {
        $this->useFilesystem('tmp');

        $resource = fopen($this->remoteFilePath(), 'r');

        $file = Facade::fromSource($resource)
            ->toDestination('tmp', 'foo')
            ->useFilename('bar')
            ->upload();
        $this->assertIsArray($file);
        $this->assertTrue($this->checkFileExists($file['disk'], $file['path']));
        $this->assertEquals('foo/bar.png', $file['path']);
        $this->assertEquals('image/png', $file['mime_type']);
        $this->assertEquals(self::TEST_FILE_SIZE, $file['size']);
        $this->assertEquals('image', $file['aggregate_type']);
    }

    public function test_it_imports_stream_objects()
    {
        $this->useFilesystem('tmp');

        $stream = new Stream(fopen($this->remoteFilePath(), 'r'));

        $file = Facade::fromSource($stream)
            ->toDestination('tmp', 'foo')
            ->useFilename('bar')
            ->upload();

        $this->assertIsArray($file);
        $this->assertTrue($this->checkFileExists($file['disk'], $file['path']));
        $this->assertEquals('tmp', $file['disk']);
        $this->assertEquals('foo/bar.png', $file['path']);
        $this->assertEquals('image/png', $file['mime_type']);
        $this->assertEquals(self::TEST_FILE_SIZE, $file['size']);
        $this->assertEquals('image', $file['aggregate_type']);
    }
}
