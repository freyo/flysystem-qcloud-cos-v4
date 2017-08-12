# flysystem-qcloud-cos-v4

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/freyo/flysystem-qcloud-cos-v4/master.svg?style=flat-square)](https://travis-ci.org/freyo/flysystem-qcloud-cos-v4)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/freyo/flysystem-qcloud-cos-v4.svg?style=flat-square)](https://scrutinizer-ci.com/g/freyo/flysystem-qcloud-cos-v4)
[![Quality Score](https://img.shields.io/scrutinizer/g/freyo/flysystem-qcloud-cos-v4.svg?style=flat-square)](https://scrutinizer-ci.com/g/freyo/flysystem-qcloud-cos-v4)
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
      'debug' => false,
  ];

  $adapter = new Adapter($config);
  $filesystem = new Filesystem($adapter);
  ```

### API

```php
bool $flysystem->write('file.md', 'contents');

bool $flysystem->writeStream('file.md', fopen('path/to/your/local/file.jpg', 'r'));

bool $flysystem->update('file.md', 'new contents');

bool $flysystem->updateStram('file.md', fopen('path/to/your/local/file.jpg', 'r'));

bool $flysystem->rename('foo.md', 'bar.md');

bool $flysystem->copy('foo.md', 'foo2.md');

bool $flysystem->delete('file.md');

bool $flysystem->has('file.md');

string|false $flysystem->read('file.md');

array $flysystem->listContents();

array $flysystem->getMetadata('file.md');

int $flysystem->getSize('file.md');

string $flysystem->getUrl('file.md'); 

string $flysystem->getMimetype('file.md');

int $flysystem->getTimestamp('file.md');

string $flysystem->getVisibility('file.md');

bool $flysystem->setVisibility('file.md', 'public'); //or 'private'
```

[Full API documentation.](http://flysystem.thephpleague.com/api/)

## Use in Laravel

1. Register the service provider in `config/app.php`:

  ```php
  'providers' => [
    // ...
    Freyo\Flysystem\QcloudCOSv4\ServiceProvider::class,
  ]
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
          'debug' => env('COSV4_DEBUG', false),
      ],
  ],
  ```

## Use in Lumen

1. Add the following code to your `bootstrap/app.php`:

  ```php
  $app->singleton('filesystem', function ($app) {
      return $app->loadComponent(
          'filesystems',
          Illuminate\Filesystem\FilesystemServiceProvider::class,
          'filesystem'
      );
  });
  ```

2. And this:
  
  ```php
  $app->register(Freyo\Flysystem\QcloudCOSv4\ServiceProvider::class);
  ```

3. Configure `.env`:
  
  ```php
  COSV4_PROTOCOL=http
  COSV4_DOMAIN=
  COSV4_APP_ID=
  COSV4_SECRET_ID=
  COSV4_SECRET_KEY=
  COSV4_TIMEOUT=60
  COSV4_BUCKET=
  COSV4_REGION=gz
  COSV4_DEBUG=true
  ```
  
### Usage

```php
$disk = Storage::disk('cosv4');

// create a file
$disk->put('avatars/1', $fileContents);

// check if a file exists
$exists = $disk->has('file.jpg');

// get timestamp
$time = $disk->lastModified('file1.jpg');

// copy a file
$disk->copy('old/file1.jpg', 'new/file1.jpg');

// move a file
$disk->move('old/file1.jpg', 'new/file1.jpg');

// get file contents
$contents = $disk->read('folder/my_file.txt');

// get url
$url = $disk->url('new/file1.jpg');

// create a file from remote(plugin support)
$disk->putRemoteFile('avatars/1', 'http://example.org/avatar.jpg');
$disk->putRemoteFileAs('avatars/1', 'http://example.org/avatar.jpg', 'file1.jpg');
```

[Full API documentation.](https://laravel.com/api/5.4/Illuminate/Contracts/Filesystem/Cloud.html)

## Region

|地区|区域表示|
|:-:|:-:|
|华南|gz|
|华北|tj|
|华东|sh|
