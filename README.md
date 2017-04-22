# flysystem-qcloud-cos-v4

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/freyo/flysystem-qcloud-cos-v4.svg?style=flat-square)](https://packagist.org/packages/freyo/flysystem-qcloud-cos-v4)
[![Total Downloads](https://img.shields.io/packagist/dt/freyo/flysystem-qcloud-cos-v4.svg?style=flat-square)](https://packagist.org/packages/freyo/flysystem-qcloud-cos-v4)

This is a Flysystem adapter for the qcloud-cos-sdk-php v4.

腾讯云COS对象存储 V4

## Attention

if you are a new registered user(after October 2016), [v4](https://packagist.org/packages/freyo/flysystem-qcloud-cos-v4) should be used.

2016年10月以后新注册的用户默认使用[V4版本](https://packagist.org/packages/freyo/flysystem-qcloud-cos-v4)

if you have used COS before October 2016, [v3](https://packagist.org/packages/freyo/flysystem-qcloud-cos-v3) can continue to use.

2016年10月之前使用COS的用户可以继续使用[V3版本](https://packagist.org/packages/freyo/flysystem-qcloud-cos-v3)

## Installation

  ```shell
  composer require freyo/flysystem-qcloud-cos-v4
  ```

## Bootstrap

  ```php
  <?php
  use Freyo\Flysystem\QcloudCOSv4\Adapter;
  use League\Flysystem\Filesystem;

  include __DIR__ . '/vendor/autoload.php';

  $config = [
      'protocol' => 'http',
      'domain' => 'your-domain',
      'app_id' => 'your-app-id',
      'secret_id' => 'your-secret-id',
      'secret_key' => 'your-secret-key',
      'timeout' => 60,
      'bucket' => 'your-bucket-name',
      'region' => 'gz',
  ],

  $adapter = new Adapter($config);
  $filesystem = new Filesystem($adapter);
  ```

## Use in Laravel

1. Register `config/app.php`:

  ```php
  Freyo\Flysystem\QcloudCOSv4\ServiceProvider::class,
  ```

2. Configure `config/filesystems.php`:

  ```php
  'disks'=>[
      'cosv4' => [
          'driver' => 'cosv4',
          'protocol' => env('COSV4_PROTOCOL', 'http'),
          'domain' => env('COSV4_DOMAIN'),
          'app_id' => env('COSV4_APP_ID'),
          'secret_id' => env('COSV4_SECRET_ID'),
          'secret_key' => env('COSV4_SECRET_KEY'),
          'timeout' => env('COSV4_TIMEOUT', 60),
          'bucket' => env('COSV4_BUCKET'),
          'region' => env('COSV4_REGION', 'gz'),
      ],
  ],
  ```
  
## Region

|地区|区域表示|
|:-:|:-:|
|华南|gz|
|华北|tj|
|华东|sh|
