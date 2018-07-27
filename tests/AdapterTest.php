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
            'protocol'   => getenv('COSV4_PROTOCOL'),
            'domain'     => getenv('COSV4_DOMAIN'),
            'app_id'     => getenv('COSV4_APP_ID'),
            'secret_id'  => getenv('COSV4_SECRET_ID'),
            'secret_key' => getenv('COSV4_SECRET_KEY'),
            'timeout'    => getenv('COSV4_TIMEOUT'),
            'bucket'     => getenv('COSV4_BUCKET'),
            'region'     => getenv('COSV4_REGION'),
            'debug'      => getenv('COSV4_DEBUG'),
        ];

        $cosApi = new Api($config);

        $adapter = new Adapter($cosApi, $config);
        
        $options = [
            'machineId' => 'COSV4_'.PHP_OS.PHP_VERSION,
        ];

        return [
            [$adapter, $config, $options],
        ];
    }

    /**
     * @dataProvider Provider
     */
    public function testWrite($adapter, $config, $options)
    {
        $this->assertTrue((bool)$adapter->write('foo/'.$options['machineId'].'/foo.md', 'content', new Config(['insertOnly' => 0])));
    }

    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testWriteInsertOnly($adapter, $config, $options)
    {
        $this->assertFalse((bool)$adapter->write('foo/'.$options['machineId'].'/foo.md', uniqid(), new Config(['insertOnly' => 1])));
    }
    
    /**
     * @dataProvider Provider
     */
    public function testWriteStream($adapter, $config, $options)
    {
        $temp = tmpfile();
        fwrite($temp, "writing to tempfile");
        $this->assertTrue((bool)$adapter->writeStream('foo/'.$options['machineId'].'/bar.md', $temp, new Config(['insertOnly' => 0])));
        fclose($temp);
    }
    
    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testWriteStreamInsertOnly($adapter, $config, $options)
    {
        $temp = tmpfile();
        fwrite($temp, uniqid());
        $this->assertFalse((bool) $adapter->writeStream('foo/'.$options['machineId'].'/bar.md', $temp, new Config(['insertOnly' => 1])));
        fclose($temp);
    }

    /**
     * @dataProvider Provider
     */
    public function testUpdate($adapter, $config, $options)
    {
        $this->assertTrue((bool)$adapter->update('foo/'.$options['machineId'].'/bar.md', uniqid(), new Config(['insertOnly' => 0])));
    }
    
    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testUpdateInsertOnly($adapter, $config, $options)
    {
        $this->assertFalse((bool)$adapter->update('foo/'.$options['machineId'].'/bar.md', uniqid(), new Config(['insertOnly' => 1])));
    }

    /**
     * @dataProvider Provider
     */
    public function testUpdateStream($adapter, $config, $options)
    {
        $temp = tmpfile();
        fwrite($temp, 'writing to tempfile');
        $this->assertTrue((bool) $adapter->updateStream('foo/'.$options['machineId'].'/bar.md', $temp, new Config(['insertOnly' => 0])));
        fclose($temp);
    }
    
    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testUpdateStreamInsertOnly($adapter, $config, $options)
    {
        $temp = tmpfile();
        fwrite($temp, uniqid());
        $this->assertFalse((bool) $adapter->updateStream('foo/'.$options['machineId'].'/bar.md', $temp, new Config(['insertOnly' => 1])));
        fclose($temp);
    }

    /**
     * @dataProvider Provider
     */
    public function testRename($adapter, $config, $options)
    {
        $this->assertTrue((bool)$adapter->write('foo/'.$options['machineId'].'/foo2.md', 'content', new Config(['insertOnly' => 0])));
        $this->assertTrue($adapter->rename('foo/'.$options['machineId'].'/foo2.md', 'foo/rename.md'));
    }
    
    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testRenameFailed($adapter, $config, $options)
    {
        $this->assertFalse($adapter->rename('foo/'.$options['machineId'].'/notexist.md', 'foo/notexist.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testCopy($adapter, $config, $options)
    {
        $this->assertTrue($adapter->copy('foo/'.$options['machineId'].'/bar.md', 'foo/copy.md'));
    }
    
    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testCopyFailed($adapter, $config, $options)
    {
        $this->assertFalse($adapter->copy('foo/'.$options['machineId'].'/notexist.md', 'foo/notexist.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testDelete($adapter, $config, $options)
    {
        $this->assertTrue((bool)$adapter->write('foo/'.$options['machineId'].'/delete.md', 'content', new Config(['insertOnly' => 0])));
        $this->assertTrue($adapter->delete('foo/'.$options['machineId'].'/delete.md'));
    }
    
    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testDeleteFailed($adapter, $config, $options)
    {
        $this->assertFalse($adapter->delete('foo/'.$options['machineId'].'/notexist.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testCreateDir($adapter, $config, $options)
    {
        $this->assertTrue((bool) $adapter->createDir('bar', new Config()));
    }

    /**
     * @dataProvider Provider
     */
    public function testDeleteDir($adapter, $config, $options)
    {
        $this->assertTrue((bool) $adapter->createDir('bar', new Config()));
        $this->assertTrue($adapter->deleteDir('bar'));
    }
    
    /**
     * @dataProvider Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv4\Exceptions\RuntimeException
     */
    public function testDeleteDirFailed($adapter, $config, $options)
    {
        $this->assertFalse($adapter->deleteDir('notexist'));
    }

    /**
     * @dataProvider Provider
     */
    public function testSetVisibility($adapter, $config, $options)
    {
        $this->assertTrue((bool)$adapter->write('foo/'.$options['machineId'].'/copy2.md', 'content', new Config(['insertOnly' => 0])));
        $this->assertTrue($adapter->setVisibility('foo/'.$options['machineId'].'/copy2.md', 'private'));
    }

    /**
     * @dataProvider Provider
     */
    public function testHas($adapter, $config, $options)
    {
        $this->assertTrue($adapter->has('foo/'.$options['machineId'].'/bar.md'));
        $this->assertFalse($adapter->has('foo/'.$options['machineId'].'/noexist.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testRead($adapter, $config, $options)
    {
        $this->assertArrayHasKey('contents', $adapter->read('foo/bar.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testGetUrl($adapter, $config, $options)
    {
        $this->assertSame(
            $config['protocol'].'://'.$config['domain'].'/foo/bar.md',
            $adapter->getUrl('foo/bar.md')
        );
    }

    /**
     * @dataProvider Provider
     */
    public function testGetTemporaryUrl($adapter, $config, $options)
    {
        $this->assertStringStartsWith(
            "http://{$config['bucket']}-{$config['app_id']}.file.myqcloud.com/foo/{$options['machineId']}/bar.md?sign=",
            $adapter->getTemporaryUrl('foo/'.$options['machineId'].'/bar.md', Carbon::now()->addMinutes(5))
        );
    }

    /**
     * @dataProvider Provider
     */
    public function testReadStream($adapter, $config, $options)
    {
        $this->assertSame(
            stream_get_contents(fopen($adapter->getUrl('foo/'.$options['machineId'].'/bar.md'), 'r')),
            stream_get_contents($adapter->readStream('foo/'.$options['machineId'].'/bar.md')['stream'])
        );
    }

    /**
     * @dataProvider Provider
     */
    public function testListContents($adapter, $config, $options)
    {
        $this->assertArrayHasKey('infos', $adapter->listContents('foo'));
    }

    /**
     * @dataProvider Provider
     */
    public function testGetMetadata($adapter, $config, $options)
    {
        $this->assertArrayHasKey('access_url', $adapter->getMetadata('foo/'.$options['machineId'].'/bar.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testGetSize($adapter, $config, $options)
    {
        $this->assertArrayHasKey('size', $adapter->getSize('foo/'.$options['machineId'].'/bar.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testGetMimetype($adapter, $config, $options)
    {
        $this->assertArrayHasKey('mimetype', $adapter->getMimetype('foo/'.$options['machineId'].'/bar.md'));
        $this->assertNotSame(['mimetype' => ''], $adapter->getMimetype('foo/'.$options['machineId'].'/bar.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testGetTimestamp($adapter, $config, $options)
    {
        $this->assertArrayHasKey('timestamp', $adapter->getTimestamp('foo/'.$options['machineId'].'/bar.md'));
        $this->assertNotSame(['timestamp' => 0], $adapter->getTimestamp('foo/'.$options['machineId'].'/bar.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testGetVisibility($adapter, $config, $options)
    {
        $this->assertTrue((bool)$adapter->write('foo/'.$options['machineId'].'/visibility.md', 'content', new Config(['insertOnly' => 0])));
        $this->assertTrue($adapter->setVisibility('foo/'.$options['machineId'].'/visibility.md', 'private'));
        $this->assertArrayHasKey('visibility', $adapter->getVisibility('foo/'.$options['machineId'].'/visibility.md'));
        $this->assertSame(['visibility' => 'private'], $adapter->getVisibility('foo/'.$options['machineId'].'/visibility.md'));
    }
}
