<?php

namespace Freyo\Flysystem\QcloudCOSv4\Plugins;

use League\Flysystem\Plugin\AbstractPlugin;

class PutRemoteFileAs extends AbstractPlugin
{
    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'putRemoteFileAs';
    }

    /**
     * @param       $path
     * @param       $remoteUrl
     * @param       $name
     * @param array $options
     *
     * @return bool
     */
    public function handle($path, $remoteUrl, $name, array $options = [])
    {
        //Get file from remote url
        $contents = (new \GuzzleHttp\Client(['verify' => false]))
            ->request('get', $remoteUrl)
            ->getBody()
            ->getContents();

        $path = trim($path.'/'.$name, '/');

        return $this->filesystem->put($path, $contents, $options) ? $path : false;
    }
}
