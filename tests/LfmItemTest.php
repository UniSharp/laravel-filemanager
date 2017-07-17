<?php

namespace Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use UniSharp\LaravelFilemanager\Lfm;
use UniSharp\LaravelFilemanager\LfmItem;
use UniSharp\LaravelFilemanager\LfmPath;
use UniSharp\LaravelFilemanager\LfmStorage;

class LfmItemTest extends TestCase
{
    private $lfm_path;
    private $lfm;

    public function setUp()
    {
        $lfm = m::mock(Lfm::class);
        $lfm->shouldReceive('getFileType')->with('baz')->andReturn('application/plain');
        $lfm->shouldReceive('getFileIcon')->with('baz')->andReturn('fa-file');
        $this->lfm = $lfm;

        $this->lfm_path = m::mock(LfmPath::class);
        $this->lfm_path->shouldReceive('path')->with('absolute')->andReturn('foo/bar.baz');
        $this->lfm_path->shouldReceive('getName')->andReturn('bar');
        $this->lfm_path->shouldReceive('isDirectory')->andReturn(false);
        $this->lfm_path->shouldReceive('size')->andReturn(1024);
        $this->lfm_path->shouldReceive('lastModified')->andReturn(0);
        $this->lfm_path->shouldReceive('setName')->andReturn($this->lfm_path);
        $this->lfm_path->shouldReceive('url')->andReturn('foo/bar');
        $this->lfm_path->shouldReceive('thumb')->andReturn($this->lfm_path);
        $this->lfm_path->shouldReceive('mimeType')->andReturn('application/plain');
    }

    public function tearDown()
    {
        m::close();

        parent::tearDown();
    }

    public function testFileName()
    {
        $this->assertEquals('bar', (new LfmItem($this->lfm_path, $this->lfm))->fileName());
    }

    public function testAbsolutePath()
    {
        $this->assertEquals('foo/bar.baz', (new LfmItem($this->lfm_path, $this->lfm))->absolutePath());
    }

    public function testIsDirectory()
    {
        $this->assertFalse((new LfmItem($this->lfm_path, $this->lfm))->isDirectory());
    }

    public function testIsFile()
    {
        $this->assertTrue((new LfmItem($this->lfm_path, $this->lfm))->isFile());
    }

    public function testIsImage()
    {
        $this->assertFalse((new LfmItem($this->lfm_path, $this->lfm))->isImage());
    }

    public function testMimeType()
    {
        $this->assertEquals('application/plain', (new LfmItem($this->lfm_path, $this->lfm))->mimeType());
    }

    public function testExtension()
    {
        $this->assertEquals('baz', (new LfmItem($this->lfm_path, $this->lfm))->extension());
    }

    // TODO: refactor
    public function testPath()
    {
        $this->assertEquals('foo/bar', (new LfmItem($this->lfm_path, $this->lfm))->path());
    }

    public function testSize()
    {
        $this->assertEquals('1.00 kB', (new LfmItem($this->lfm_path, $this->lfm))->size());
    }

    public function testLastModified()
    {
        $this->assertEquals(0, (new LfmItem($this->lfm_path, $this->lfm))->lastModified());
    }

    public function testIcon()
    {
        $this->assertEquals('fa-file', (new LfmItem($this->lfm_path, $this->lfm))->icon());
        return;

        // $path1 = m::mock(LfmPath::class);
        // $path1->shouldReceive('path')->with('absolute')->andReturn('foo/bar');
        // $path1->shouldReceive('isDirectory')->andReturn(false);
        // $path1->shouldReceive('mimeType')->andReturn('image/png');

        // $path2 = m::mock(LfmPath::class);
        // $path2->shouldReceive('path')->with('absolute')->andReturn('foo/baz');
        // $path2->shouldReceive('isDirectory')->andReturn(false);
        // $path2->shouldReceive('mimeType')->andReturn('application/plain');

        // $path3 = m::mock(LfmPath::class);
        // $path3->shouldReceive('path')->with('absolute')->andReturn('foo/biz');
        // $path3->shouldReceive('isDirectory')->andReturn(true);

        // $this->assertEquals('fa-image',    (new LfmItem($path1))->icon());
        // $this->assertEquals('fa-file',     (new LfmItem($path2))->icon());
        // $this->assertEquals('fa-folder-o', (new LfmItem($path3))->icon());
    }

    public function testHumanFilesize()
    {
        $item = new LfmItem($this->lfm_path, $this->lfm);

        $this->assertEquals('1.00 kB', $item->humanFilesize(1024));
        $this->assertEquals('1.00 MB', $item->humanFilesize(1024 ** 2));
        $this->assertEquals('1.00 GB', $item->humanFilesize(1024 ** 3));
        $this->assertEquals('1.00 TB', $item->humanFilesize(1024 ** 4));
        $this->assertEquals('1.00 PB', $item->humanFilesize(1024 ** 5));
        $this->assertEquals('1.00 EB', $item->humanFilesize(1024 ** 6));
    }
}
