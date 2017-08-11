<?php

namespace Freyo\Flysystem\QcloudCOSv4;

use Freyo\Flysystem\QcloudCOSv4\Plugins\GetUrl;
use Freyo\Flysystem\QcloudCOSv4\Plugins\PutRemoteFile;
use Freyo\Flysystem\QcloudCOSv4\Plugins\PutRemoteFileAs;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\Facades\Storage;
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
        $source = realpath(__DIR__ . '/filesystems.php');

        if ($this->app instanceof LaravelApplication) {
            if ($this->app->runningInConsole()) {
                $this->publishes([
                    $source => config_path('filesystems.php'),
                ]);
            }
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('filesystems');
        }

        $this->app->make('filesystem')
                  ->extend('cosv4', function ($app, $config) {
                      return new Filesystem(new Adapter($config));
                  })
                  ->disk('cosv4')
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
            __DIR__.'/filesystems.php', 'filesystems'
        );
    }
}
