<?php

namespace Udhuong\LaravelUploadFile;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Udhuong\LaravelUploadFile\Commands\UploadFileCommand;
use Udhuong\LaravelUploadFile\SourceAdapters\SourceAdapterFactory;
use Udhuong\LaravelUploadFile\UrlGenerators\UrlGeneratorFactory;

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
        $this->registerUrlGeneratorFactory();
        $this->registerConsoleCommands();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/config/upload_file.php',
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
        $this->app->singleton('upload_file.source.factory', function (Container $app){
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
        $this->app->alias('upload_file.source.factory', SourceAdapterFactory::class);
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
                $app['upload_file.source.factory'],
                $app['config']->get('upload_file')
            );
        });
        $this->app->alias('upload_file', FileUploader::class);
    }

    /**
     * Bind the Media Uploader to the container.
     * @return void
     */
    public function registerUrlGeneratorFactory(): void
    {
        $this->app->singleton('upload_file.url.factory', function (Container $app) {
            $factory = new UrlGeneratorFactory;

            $config = $app['config']->get('upload_file.url_generators', []);
            foreach ($config as $driver => $generator) {
                $factory->setGeneratorForFilesystemDriver($generator, $driver);
            }

            return $factory;
        });
        $this->app->alias('upload_file.url.factory', UrlGeneratorFactory::class);
    }

    /**
     * Add package commands to artisan console.
     * @return void
     */
    public function registerConsoleCommands(): void
    {
        $this->commands([
            UploadFileCommand::class
        ]);
    }
}
