<?php

namespace Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use UniSharp\LaravelFilemanager\Lfm;
use Unisharp\Laravelfilemanager\LfmItem;
use Unisharp\Laravelfilemanager\LfmStorage;
use Illuminate\Contracts\Config\Repository as Config;

class LfmItemTest extends TestCase
{
    public function tearDown()
    {
        m::close();

        parent::tearDown();
    }

    public function testFileName()
    {
        $storage = m::mock(LfmStorage::class);

        $item = new LfmItem($storage, 'foo/bar');

        $this->assertEquals('bar', $item->fileName());
    }

    public function testAbsolutePath()
    {
        $storage = m::mock(LfmStorage::class);
        $storage->disk_root = '/app';

        $item = new LfmItem($storage, 'foo/bar');

        $this->assertEquals('/app/foo/bar', $item->absolutePath());
    }

    public function testIsDirectory()
    {
        $storage = m::mock(LfmStorage::class);
        $storage->shouldReceive('isDirectory')->with('foo/bar')->andReturn(false);

        $item = new LfmItem($storage, 'foo/bar');

        $this->assertFalse($item->isDirectory());
    }

    public function testIsFile()
    {
        $storage = m::mock(LfmStorage::class);
        $storage->shouldReceive('isDirectory')->with('foo/bar')->andReturn(false);

        $item = new LfmItem($storage, 'foo/bar');

        $this->assertTrue($item->isFile());
    }

    public function testMimeType()
    {
        $storage = m::mock(LfmStorage::class);
        $storage->shouldReceive('mimeType')->with('foo/bar')->once()->andReturn($mime = 'application/plain');

        $item = new LfmItem($storage, 'foo/bar');

        $this->assertEquals('application/plain', $item->mimeType());
    }

    public function testExtension()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('extension')->with('/app/foo/bar.baz')->once()->andReturn($ext = 'baz');

        $storage = m::mock(LfmStorage::class);
        $storage->disk = $disk;
        $storage->disk_root = '/app';

        $item = new LfmItem($storage, 'foo/bar.baz');

        $this->assertEquals($ext, $item->extension());
    }

    public function testIsImage()
    {
        $storage = m::mock(LfmStorage::class);
        $storage->shouldReceive('mimeType')->with('foo/bar')->once()->andReturn($mime = 'application/plain');

        $item = new LfmItem($storage, 'foo/bar');

        $this->assertFalse($item->isImage());
    }

    // TODO: refactor
    public function testUrl()
    {
        $lfm = m::mock(Lfm::class);
        $lfm->shouldReceive('url')->with('/app/foo/bar')->once()->andReturn($url = 'http://localhost/app/foo/bar');

        $storage = m::mock(LfmStorage::class);
        $storage->disk_root = '/app';
        $storage->lfm = $lfm;

        $item = new LfmItem($storage, 'foo/bar');

        $this->assertEquals($url, $item->url());
    }

    public function testSize()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('size')->with('foo/bar')->once()->andReturn(1024);

        $storage = m::mock(LfmStorage::class);
        $storage->disk = $disk;

        $item = new LfmItem($storage, 'foo/bar');

        $this->assertEquals('1.00 kB', $item->size());
    }

    public function testLastModified()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('lastModified')->with('foo/bar')->once()->andReturn(0);

        $storage = m::mock(LfmStorage::class);
        $storage->disk = $disk;

        $item = new LfmItem($storage, 'foo/bar');

        $this->assertEquals(0, $item->lastModified());
    }

    public function testIcon()
    {
        $disk = m::mock('disk');
        // $disk->shouldReceive('isDirectory')->with('foo/biz')->once()->andReturn(true);
        // $disk->shouldReceive('isDirectory')->with('/app/foo/bar')->times(2)->andReturn(false);
        $disk->shouldReceive('extension')->with('foo/baz')->once()->andReturn('');
        $disk->shouldReceive('extension')->with('foo/biz')->once()->andReturn('');

        $lfm = m::mock(Lfm::class);
        $lfm->shouldReceive('getFileIcon')->with('')->once()->andReturn('fa-file');

        $storage = m::mock(LfmStorage::class);
        $storage->disk = $disk;
        $storage->disk_root = '/app';
        $storage->lfm = $lfm;
        $storage->shouldReceive('mimeType')->with('foo/bar')->once()->andReturn('image/png');
        $storage->shouldReceive('mimeType')->with('foo/baz')->once()->andReturn('application/plain');
        $storage->shouldReceive('mimeType')->with('foo/biz')->once()->andReturn('');
        $storage->shouldReceive('isDirectory')->with('foo/bar')->andReturn(false);
        $storage->shouldReceive('isDirectory')->with('foo/baz')->andReturn(false);
        $storage->shouldReceive('isDirectory')->with('foo/biz')->andReturn(true);

        $item1 = new LfmItem($storage, 'foo/bar');
        $item2 = new LfmItem($storage, 'foo/baz');
        $item3 = new LfmItem($storage, 'foo/biz');

        $this->assertEquals('fa-folder-o', $item3->icon());
        $this->assertEquals('fa-image', $item1->icon());
        $this->assertEquals('fa-file', $item2->icon());
    }

    public function testHumanFilesize()
    {
        $item = new LfmItem(m::mock(LfmStorage::class), 'foo/bar');

        $this->assertEquals('1.00 kB', $item->humanFilesize(1024));
        $this->assertEquals('1.00 MB', $item->humanFilesize(1024 ** 2));
        $this->assertEquals('1.00 GB', $item->humanFilesize(1024 ** 3));
        $this->assertEquals('1.00 TB', $item->humanFilesize(1024 ** 4));
        $this->assertEquals('1.00 PB', $item->humanFilesize(1024 ** 5));
        $this->assertEquals('1.00 EB', $item->humanFilesize(1024 ** 6));
    }
}
