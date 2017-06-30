<?php

namespace Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use UniSharp\LaravelFilemanager\Lfm;
use Unisharp\Laravelfilemanager\LfmItem;
use Unisharp\Laravelfilemanager\LfmStorage;

class LfmItemTest extends TestCase
{
    public function tearDown()
    {
        m::close();

        parent::tearDown();
    }

    public function testFileName()
    {
        $item = new LfmItem(m::mock(LfmStorage::class), 'foo/bar');

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
        $disk = m::mock('disk');
        $disk->shouldReceive('isDirectory')->with('/app/foo/bar')->once()->andReturn(false);

        $storage = m::mock(LfmStorage::class);
        $storage->disk = $disk;
        $storage->disk_root = '/app';

        $item = new LfmItem($storage, 'foo/bar');

        $this->assertFalse($item->isDirectory());
    }

    public function testIsFile()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('isDirectory')->with('/app/foo/bar')->once()->andReturn(false);

        $storage = m::mock(LfmStorage::class);
        $storage->disk = $disk;
        $storage->disk_root = '/app';

        $item = new LfmItem($storage, 'foo/bar');

        $this->assertTrue($item->isFile());
    }

    public function testMimeType()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('mimeType')->with('/app/foo/bar')->once()->andReturn($mime = 'application/plain');

        $storage = m::mock(LfmStorage::class);
        $storage->disk = $disk;
        $storage->disk_root = '/app';

        $item = new LfmItem($storage, 'foo/bar');

        $this->assertEquals($mime, $item->mimeType());
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
        $disk = m::mock('disk');
        $disk->shouldReceive('mimeType')->with('/app/foo/bar')->once()->andReturn('application/plain');

        $storage = m::mock(LfmStorage::class);
        $storage->disk = $disk;
        $storage->disk_root = '/app';

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
        $disk->shouldReceive('size')->with('/app/foo/bar')->once()->andReturn(1024);

        $lfm = m::mock(Lfm::class);
        $lfm->shouldReceive('humanFilesize')->with(1024)->once()->andReturn($size = '1.00 kB');

        $storage = m::mock(LfmStorage::class);
        $storage->disk = $disk;
        $storage->disk_root = '/app';
        $storage->lfm = $lfm;

        $item = new LfmItem($storage, 'foo/bar');

        $this->assertEquals($size, $item->size());
    }

    public function testLastModified()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('lastModified')->with('/app/foo/bar')->once()->andReturn(0);

        $storage = m::mock(LfmStorage::class);
        $storage->disk = $disk;
        $storage->disk_root = '/app';

        $item = new LfmItem($storage, 'foo/bar');

        $this->assertEquals(0, $item->lastModified());
    }

    public function testIcon()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('isDirectory')->with('/app/foo/bar')->once()->andReturn(true);
        $disk->shouldReceive('isDirectory')->with('/app/foo/bar')->times(2)->andReturn(false);
        $disk->shouldReceive('mimeType')->with('/app/foo/bar')->once()->andReturn('image/png');
        $disk->shouldReceive('mimeType')->with('/app/foo/bar')->once()->andReturn('application/plain');
        $disk->shouldReceive('extension')->with('/app/foo/bar')->once()->andReturn('');

        $lfm = m::mock(Lfm::class);
        $lfm->shouldReceive('getFileIcon')->with('')->once()->andReturn('fa-file');

        $storage = m::mock(LfmStorage::class);
        $storage->disk = $disk;
        $storage->disk_root = '/app';
        $storage->lfm = $lfm;

        $item = new LfmItem($storage, 'foo/bar');

        $this->assertEquals('fa-folder-o', $item->icon());
        $this->assertEquals('fa-image', $item->icon());
        $this->assertEquals('fa-file', $item->icon());
    }

    public function testHumanFilesize()
    {
        $lfm = new Lfm(m::mock(Config::class));

        $this->assertEquals('1.00 kB', $lfm->humanFilesize(1024));
        $this->assertEquals('1.00 MB', $lfm->humanFilesize(1024 ** 2));
        $this->assertEquals('1.00 GB', $lfm->humanFilesize(1024 ** 3));
        $this->assertEquals('1.00 TB', $lfm->humanFilesize(1024 ** 4));
        $this->assertEquals('1.00 PB', $lfm->humanFilesize(1024 ** 5));
        $this->assertEquals('1.00 EB', $lfm->humanFilesize(1024 ** 6));
    }
}
