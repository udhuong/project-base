<?php

namespace Udhuong\LaravelUploadFile;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Udhuong\LaravelUploadFile\SourceAdapters\SourceAdapterFactory;

class UploadFileServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $root = dirname(__DIR__);
        $this->publishes(
            [
                $root. '/config/upload_file.php' => config_path('upload_file.php')
            ],
            'config'
        );

        $this->registerSourceAdapterFactory();
        $this->registerUploader();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            dir(__DIR__) . '/config/upload_file.php',
            'upload_file'
        );
    }

    /**
     * Bind an instance of the Source Adapter Factory to the container.
     *
     * Attaches the default adapter types
     * @return void
     */
    public function registerSourceAdapterFactory(): void
    {
        $this->app->singleton('upload_file_source_factory', function (Container $app){
            $factory = new SourceAdapterFactory();

            $classAdapters = $app['config']->get('upload_file.source_adapters.class', []);
            foreach ($classAdapters as $source => $adapter) {
                $factory->setAdapterForClass($adapter, $source);
            }

            $patternAdapters = $app['config']->get('upload_file.source_adapters.pattern', []);
            foreach ($patternAdapters as $source => $adapter) {
                $factory->setAdapterForPattern($adapter, $source);
            }

            return $factory;
        });
        $this->app->alias('upload_file_source_factory', SourceAdapterFactory::class);
    }

    /**
     * Bind the Media Uploader to the container.
     * @return void
     */
    public function registerUploader(): void
    {
        $this->app->bind('upload_file', function (Container $app) {
            return new FileUploader(
                $app['filesystem'],
                $app['upload_file_source_factory'],
                $app['config']->get('upload_file')
            );
        });
        $this->app->alias('upload_file', FileUploader::class);
    }
}
