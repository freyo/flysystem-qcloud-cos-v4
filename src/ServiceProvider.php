<?php

namespace Freyo\Flysystem\QcloudCOSv4;

use Freyo\Flysystem\QcloudCOSv4\Plugins\GetUrl;
use Freyo\Flysystem\QcloudCOSv4\Plugins\PutRemoteFile;
use Freyo\Flysystem\QcloudCOSv4\Plugins\PutRemoteFileAs;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use League\Flysystem\Filesystem;

/**
 * Class ServiceProvider.
 */
class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/filesystems.php' => config_path('filesystems.php'),
        ]);

        Storage::extend('cosv4', function ($app, $config) {
            return new Filesystem(new Adapter($config));
        });

        Storage::disk('cosv4')
               ->addPlugin(new PutRemoteFile())
               ->addPlugin(new PutRemoteFileAs())
               ->addPlugin(new GetUrl());
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/filesystems.php', 'filesystems'
        );
    }
}
