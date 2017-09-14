<?php

namespace Freyo\Flysystem\QcloudCOSv4;

use Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\Util;
use QCloud\Cos\Api;

/**
 * Class Adapter.
 */
class Adapter extends AbstractAdapter
{
    /**
     * @var Api
     */
    protected $cosApi;

    /**
     * @var
     */
    protected $bucket;

    /**
     * @var
     */
    protected $debug;

    /**
     * Adapter constructor.
     *
     * @param Api   $cosApi
     * @param array $config
     */
    public function __construct(Api $cosApi, array $config)
    {
        $this->cosApi = $cosApi;

        $this->bucket = $config['bucket'];
        $this->debug  = $config['debug'];

        $this->setPathPrefix($config['protocol'] . '://' . $config['domain'] . '/');
    }

    /**
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getUrl($path)
    {
        return $this->applyPathPrefix($path);
    }

    /**
     * @param string $path
     * @param string $contents
     * @param Config $config
     *
     * @throws RuntimeException
     *
     * @return array|bool
     */
    public function write($path, $contents, Config $config)
    {
        $temporaryPath = $this->createTemporaryFile($contents);

        $response = $this->cosApi->upload($this->getBucket(), $temporaryPath, $path,
            null, null, $config->get('insertOnly', 1));

        $response = $this->normalizeResponse($response);

        if (false !== $response) {
            $this->setContentType($path, $contents);
        }

        return $response;
    }

    /**
     * @param string   $path
     * @param resource $resource
     * @param Config   $config
     *
     * @throws RuntimeException
     *
     * @return array|bool
     */
    public function writeStream($path, $resource, Config $config)
    {
        $uri = stream_get_meta_data($resource)['uri'];

        $response = $this->cosApi->upload($this->getBucket(), $uri, $path,
            null, null, $config->get('insertOnly', 1));

        $response = $this->normalizeResponse($response);

        if (false !== $response) {
            $this->setContentType($path, stream_get_contents($resource));
        }

        return $response;
    }

    /**
     * @param string $path
     * @param string $contents
     * @param Config $config
     *
     * @throws RuntimeException
     *
     * @return array|bool
     */
    public function update($path, $contents, Config $config)
    {
        $temporaryPath = $this->createTemporaryFile($contents);

        $response = $this->cosApi->upload($this->getBucket(), $temporaryPath, $path,
            null, null, $config->get('insertOnly', 0));

        $response = $this->normalizeResponse($response);

        if (false !== $response) {
            $this->setContentType($path, $contents);
        }

        return $response;
    }

    /**
     * @param string   $path
     * @param resource $resource
     * @param Config   $config
     *
     * @throws RuntimeException
     *
     * @return array|bool
     */
    public function updateStream($path, $resource, Config $config)
    {
        $uri = stream_get_meta_data($resource)['uri'];

        $response = $this->cosApi->upload($this->getBucket(), $uri, $path,
            null, null, $config->get('insertOnly', 0));

        $response = $this->normalizeResponse($response);

        if (false !== $response) {
            $this->setContentType($path, stream_get_contents($resource));
        }

        return $response;
    }

    /**
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function rename($path, $newpath)
    {
        return (bool)$this->normalizeResponse(
            $this->cosApi->moveFile($this->getBucket(), $path, $newpath, true)
        );
    }

    /**
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath)
    {
        return (bool)$this->normalizeResponse(
            $this->cosApi->copyFile($this->getBucket(), $path, $newpath, true)
        );
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function delete($path)
    {
        return (bool)$this->normalizeResponse(
            $this->cosApi->delFile($this->getBucket(), $path)
        );
    }

    /**
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname)
    {
        return (bool)$this->normalizeResponse(
            $this->cosApi->delFolder($this->getBucket(), $dirname)
        );
    }

    /**
     * @param string $dirname
     * @param Config $config
     *
     * @return array|bool
     */
    public function createDir($dirname, Config $config)
    {
        return $this->normalizeResponse(
            $this->cosApi->createFolder($this->getBucket(), $dirname)
        );
    }

    /**
     * @param string $path
     * @param string $visibility
     *
     * @return bool
     */
    public function setVisibility($path, $visibility)
    {
        $visibility = ($visibility === AdapterInterface::VISIBILITY_PUBLIC)
            ? 'eWPrivateRPublic' : 'eWRPrivate';

        return (bool)$this->normalizeResponse(
            $this->cosApi->update($this->getBucket(), $path, null, $visibility)
        );
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function has($path)
    {
        try {
            return (bool)$this->getMetadata($path);
        } catch (RuntimeException $exception) {
            return false;
        }
    }

    /**
     * @param string $path
     *
     * @return array|bool
     */
    public function read($path)
    {
        $contents = file_get_contents($this->applyPathPrefix($path));

        return $contents !== false ? compact('contents') : false;
    }

    /**
     * @param string $path
     *
     * @return array|bool
     */
    public function readStream($path)
    {
        $stream = fopen($this->applyPathPrefix($path), 'r');

        return $stream !== false ? compact('stream') : false;
    }

    /**
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array|bool
     */
    public function listContents($directory = '', $recursive = false)
    {
        return $this->normalizeResponse(
            $this->cosApi->listFolder($this->getBucket(), $directory)
        );
    }

    /**
     * @param string $path
     *
     * @return array|bool
     */
    public function getMetadata($path)
    {
        return $this->normalizeResponse(
            $this->cosApi->stat($this->getBucket(), $path)
        );
    }

    /**
     * @param string $path
     *
     * @return array|bool
     */
    public function getSize($path)
    {
        $stat = $this->getMetadata($path);

        return isset($stat['filesize']) ? ['size' => $stat['filesize']] : false;
    }

    /**
     * @param string $path
     *
     * @return array|bool
     */
    public function getMimetype($path)
    {
        $stat = $this->getMetadata($path);

        return isset($stat['custom_headers']['Content-Type'])
            ? ['mimetype' => $stat['custom_headers']['Content-Type']] : false;
    }

    /**
     * @param string $path
     *
     * @return array|bool
     */
    public function getTimestamp($path)
    {
        $stat = $this->getMetadata($path);

        return isset($stat['ctime']) ? ['timestamp' => $stat['ctime']] : false;
    }

    /**
     * @param string $path
     *
     * @return array|bool
     */
    public function getVisibility($path)
    {
        $stat = $this->getMetadata($path);

        if (isset($stat['authority']) && $stat['authority'] === 'eWPrivateRPublic') {
            return ['visibility' => AdapterInterface::VISIBILITY_PUBLIC];
        }

        if (isset($stat['authority']) && $stat['authority'] === 'eWRPrivate') {
            return ['visibility' => AdapterInterface::VISIBILITY_PRIVATE];
        }

        return false;
    }

    /**
     * Creates a temporary file.
     *
     * @param string $content
     *
     * @throws RuntimeException
     *
     * @return string
     */
    protected function createTemporaryFile($content)
    {
        $temporaryPath = $this->getTemporaryPath();

        if (false === $temporaryPath) {
            throw new RuntimeException("Unable to create temporary file in '{$temporaryPath}'.");
        }

        file_put_contents($temporaryPath, $content);

        // The file is automatically removed when closed, or when the script ends.
        register_shutdown_function(function () use ($temporaryPath) {
            unlink($temporaryPath);
        });

        return $temporaryPath;
    }

    /**
     * Gets a temporary file path.
     *
     * @return bool|string
     */
    protected function getTemporaryPath()
    {
        return tempnam(sys_get_temp_dir(), uniqid('tencentyun', true));
    }

    /**
     * @param string $path
     * @param string $content
     *
     * @return bool
     */
    protected function setContentType($path, $content)
    {
        $custom_headers = [
            'Content-Type' => Util::guessMimeType($path, $content),
        ];

        return $this->normalizeResponse(
            $this->cosApi->update($this->getBucket(), $path, null, null, $custom_headers)
        );
    }

    /**
     * @param $response
     *
     * @throws RuntimeException
     *
     * @return mixed
     */
    protected function normalizeResponse($response)
    {
        if ($response['code'] == 0) {
            return isset($response['data']) ? $response['data'] : true;
        }

        if ($this->debug) {
            throw new RuntimeException($response['message'], $response['code']);
        }

        return false;
    }
}
