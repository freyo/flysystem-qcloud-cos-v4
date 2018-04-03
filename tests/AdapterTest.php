<?php

namespace Freyo\Flysystem\QcloudCOSv4\Tests;

use Carbon\Carbon;
use Freyo\Flysystem\QcloudCOSv4\Adapter;
use League\Flysystem\Config;
use PHPUnit\Framework\TestCase;
use QCloud\Cos\Api;

class AdapterTest extends TestCase
{
    public function Provider()
    {
        $config = [
            'protocol'   => 'http',
            'domain'     => 'your-domain',
            'app_id'     => 'your-app-id',
            'secret_id'  => 'your-secret-id',
            'secret_key' => 'your-secret-key',
            'timeout'    => 60,
            'bucket'     => 'your-bucket-name',
            'region'     => 'gz',
            'debug'      => true,
        ];

        $cosApi = new Api($config);

        $adapter = new Adapter($cosApi, $config);

        return [
            [$adapter, $config],
        ];
    }

    /**
     * @dataProvider Provider
     */
    public function testWrite($adapter)
    {
        //$this->assertTrue((bool)$adapter->write('foo/foo.md', 'content', new Config(['insertOnly' => 0])));
        //$this->assertFalse((bool)$adapter->write('foo/foo.md', uniqid(), new Config(['insertOnly' => 1])));
    }

    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testWriteStream($adapter)
    {
        //$temp = tmpfile();
        //fwrite($temp, "writing to tempfile");
        //$this->assertTrue((bool)$adapter->writeStream('foo/bar.md', $temp, new Config(['insertOnly' => 0])));
        //fclose($temp);

        $temp = tmpfile();
        fwrite($temp, uniqid());
        $this->assertFalse((bool) $adapter->writeStream('foo/bar.md', $temp, new Config(['insertOnly' => 1])));
        fclose($temp);
    }

    /**
     * @dataProvider Provider
     */
    public function testUpdate($adapter)
    {
        //$this->assertTrue((bool)$adapter->update('foo/bar.md', uniqid(), new Config(['insertOnly' => 0])));
        //$this->assertFalse((bool)$adapter->update('foo/bar.md', uniqid(), new Config(['insertOnly' => 1])));
    }

    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testUpdateStream($adapter)
    {
        $temp = tmpfile();
        fwrite($temp, 'writing to tempfile');
        $this->assertTrue((bool) $adapter->updateStream('foo/bar.md', $temp, new Config(['insertOnly' => 0])));
        fclose($temp);

        $temp = tmpfile();
        fwrite($temp, uniqid());
        $this->assertFalse((bool) $adapter->updateStream('foo/bar.md', $temp, new Config(['insertOnly' => 1])));
        fclose($temp);
    }

    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testRename($adapter)
    {
        $this->assertTrue($adapter->rename('foo/foo.md', 'foo/rename.md'));
        $this->assertFalse($adapter->rename('foo/notexist.md', 'foo/notexist.md'));
    }

    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testCopy($adapter)
    {
        $this->assertTrue($adapter->copy('foo/bar.md', 'foo/copy.md'));
        $this->assertFalse($adapter->copy('foo/notexist.md', 'foo/notexist.md'));
    }

    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testDelete($adapter)
    {
        $this->assertTrue($adapter->delete('foo/rename.md'));
        $this->assertFalse($adapter->delete('foo/notexist.md'));
    }

    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testCreateDir($adapter)
    {
        $this->assertTrue((bool) $adapter->createDir('bar', new Config()));
        $this->assertFalse((bool) $adapter->createDir('bar', new Config()));
    }

    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testDeleteDir($adapter)
    {
        $this->assertTrue($adapter->deleteDir('bar'));
        $this->assertFalse($adapter->deleteDir('notexist'));
    }

    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testSetVisibility($adapter)
    {
        $this->assertTrue($adapter->setVisibility('foo/copy.md', 'private'));
    }

    /**
     * @dataProvider Provider
     */
    public function testHas($adapter)
    {
        //$this->assertTrue($adapter->has('foo/bar.md'));
        $this->assertFalse($adapter->has('foo/noexist.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testRead($adapter)
    {
        //$this->assertArrayHasKey('contents', $adapter->read('foo/bar.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testGetUrl($adapter, $config)
    {
        $this->assertSame(
            $config['protocol'].'://'.$config['domain'].'/foo/bar.md',
            $adapter->getUrl('foo/bar.md')
        );
    }

    /**
     * @dataProvider Provider
     */
    public function testGetTemporaryUrl($adapter, $config)
    {
        $this->assertStringStartsWith(
            "http://{$config['bucket']}-{$config['app_id']}.file.myqcloud.com/foo/bar.md?sign=",
            $adapter->getTemporaryUrl('foo/bar.md', Carbon::now()->addMinutes(5))
        );
    }

    /**
     * @dataProvider Provider
     */
    public function testReadStream($adapter)
    {
        //$this->assertSame(
        //    stream_get_contents(fopen($adapter->getUrl('foo/bar.md'), 'r')),
        //    stream_get_contents($adapter->readStream('foo/bar.md')['stream'])
        //);
    }

    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testListContents($adapter)
    {
        $this->assertArrayHasKey('infos', $adapter->listContents('foo'));
    }

    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testGetMetadata($adapter)
    {
        $this->assertArrayHasKey('access_url', $adapter->getMetadata('foo/bar.md'));
    }

    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testGetSize($adapter)
    {
        $this->assertArrayHasKey('size', $adapter->getSize('foo/bar.md'));
    }

    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testGetMimetype($adapter)
    {
        $this->assertNotSame(['mimetype' => ''], $adapter->getMimetype('foo/bar.md'));
    }

    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testGetTimestamp($adapter)
    {
        $this->assertNotSame(['timestamp' => 0], $adapter->getTimestamp('foo/bar.md'));
    }

    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testGetVisibility($adapter)
    {
        $this->assertSame(['visibility' => 'private'], $adapter->getVisibility('foo/copy.md'));
    }
}
