<?php

namespace Freyo\Flysystem\QcloudCOSv4;

use Freyo\Flysystem\QcloudCOSv4\Plugins\GetUrl;
use Freyo\Flysystem\QcloudCOSv4\Plugins\PutRemoteFile;
use Freyo\Flysystem\QcloudCOSv4\Plugins\PutRemoteFileAs;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
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
        if ($this->app instanceof LumenApplication) {
            $this->app->configure('filesystems');
        }

        $this->app->make('filesystem')
                  ->extend('cosv4', function ($app, $config) {
                      $flysystem = new Filesystem(new Adapter($config));

                      $flysystem->addPlugin(new PutRemoteFile());
                      $flysystem->addPlugin(new PutRemoteFileAs());
                      $flysystem->addPlugin(new GetUrl());

                      return $flysystem;
                  });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/filesystems.php', 'filesystems'
        );
    }
}
