<?php

namespace Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use UniSharp\LaravelFilemanager\Lfm;
use UniSharp\LaravelFilemanager\LfmItem;
use UniSharp\LaravelFilemanager\LfmPath;

class LfmItemTest extends TestCase
{
    private $lfm_path;
    private $lfm;

    public function setUp()
    {
        $this->lfm = m::mock(Lfm::class);

        $this->lfm_path = m::mock(LfmPath::class);
        $this->lfm_path->shouldReceive('thumb')->andReturn($this->lfm_path);
    }

    public function tearDown()
    {
        m::close();

        parent::tearDown();
    }

    public function testMagicGet()
    {
        $this->lfm_item = new LfmItem($this->lfm_path, m::mock(Lfm::class));

        $this->lfm_item->attributes['foo'] = 'bar';

        $this->assertEquals('bar', $this->lfm_item->foo);
    }

    public function testName()
    {
        $this->lfm_path->shouldReceive('getName')->andReturn('bar');

        $item = new LfmItem($this->lfm_path, $this->lfm);

        $this->assertEquals('bar', $item->name());
    }

    public function testAbsolutePath()
    {
        $this->lfm_path->shouldReceive('path')->with('absolute')->andReturn('foo/bar.baz');

        $item = new LfmItem($this->lfm_path, $this->lfm);

        $this->assertEquals('foo/bar.baz', $item->path());
    }

    public function testIsDirectory()
    {
        $this->lfm_path->shouldReceive('isDirectory')->andReturn(false);

        $item = new LfmItem($this->lfm_path, $this->lfm);

        $this->assertFalse($item->isDirectory());
    }

    public function testIsFile()
    {
        $this->lfm_path->shouldReceive('isDirectory')->andReturn(false);

        $item = new LfmItem($this->lfm_path, $this->lfm);

        $this->assertTrue($item->isFile());
    }

    public function testIsImage()
    {
        $this->lfm_path->shouldReceive('mimeType')->andReturn('application/plain')->shouldReceive('isDirectory')
            ->andReturn(false);

        $item = new LfmItem($this->lfm_path, $this->lfm);

        $this->assertFalse($item->isImage());
    }

    public function testMimeType()
    {
        $this->lfm_path->shouldReceive('mimeType')->andReturn('application/plain');

        $item = new LfmItem($this->lfm_path, $this->lfm);

        $this->assertEquals('application/plain', $item->mimeType());
    }

    public function testType()
    {
        $this->lfm_path->shouldReceive('isDirectory')->andReturn(false);
        $this->lfm_path->shouldReceive('mimeType')->andReturn('application/plain');
        $this->lfm_path->shouldReceive('path')->with('absolute')->andReturn('foo/bar.baz');
        $this->lfm_path->shouldReceive('extension')->andReturn('baz');

        $this->lfm->shouldReceive('getFileType')->with('baz')->andReturn('File');

        $item = new LfmItem($this->lfm_path, $this->lfm);

        $this->assertEquals('File', $item->type());
    }

    public function testExtension()
    {
        $this->lfm_path->shouldReceive('path')->with('absolute')->andReturn('foo/bar.baz');
        $this->lfm_path->shouldReceive('extension')->andReturn('baz');

        $item = new LfmItem($this->lfm_path, $this->lfm);

        $this->assertEquals('baz', $item->extension());
    }

    public function testThumbUrl()
    {
        $this->lfm_path->shouldReceive('isDirectory')->andReturn(false);
        $this->lfm_path->shouldReceive('mimeType')->andReturn('application/plain');

        $item = new LfmItem($this->lfm_path, $this->lfm);

        $this->assertNull($item->thumbUrl());
    }

    // TODO: refactor
    public function testUrl()
    {
        $this->lfm_path->shouldReceive('isDirectory')->andReturn(false);
        $this->lfm_path->shouldReceive('getName')->andReturn('bar');
        $this->lfm_path->shouldReceive('setName')->andReturn($this->lfm_path);
        $this->lfm_path->shouldReceive('url')->andReturn('foo/bar');

        $item = new LfmItem($this->lfm_path, $this->lfm);

        $this->assertEquals('foo/bar', $item->url());
    }

    public function testSize()
    {
        $this->lfm_path->shouldReceive('size')->andReturn(1024);
        $this->lfm_path->shouldReceive('isDirectory')->andReturn(false);

        $item = new LfmItem($this->lfm_path, $this->lfm);

        $this->assertEquals('1.00 kB', $item->size());
    }

    public function testTime()
    {
        $this->lfm_path->shouldReceive('lastModified')->andReturn(0)->shouldReceive('isDirectory')
            ->andReturn(false);

        $item = new LfmItem($this->lfm_path, $this->lfm);

        $this->assertEquals(0, $item->time());
    }

    public function testIcon()
    {
        $this->lfm_path->shouldReceive('isDirectory')->andReturn(false);
        $this->lfm_path->shouldReceive('mimeType')->andReturn('application/plain');
        $this->lfm_path->shouldReceive('path')->with('absolute')->andReturn('foo/bar.baz');
        $this->lfm_path->shouldReceive('extension')->andReturn('baz');

        $this->lfm->shouldReceive('getFileIcon')->with('baz')->andReturn('fa-file');

        $item = new LfmItem($this->lfm_path, $this->lfm);

        $this->assertEquals('baz', $item->icon());

        // $path1 = m::mock(LfmPath::class);
        // $path1->shouldReceive('path')->with('absolute')->andReturn('foo/bar');
        // $path1->shouldReceive('isDirectory')->andReturn(false);
        // $path1->shouldReceive('mimeType')->andReturn('image/png');

        // $path3 = m::mock(LfmPath::class);
        // $path3->shouldReceive('path')->with('absolute')->andReturn('foo/biz');
        // $path3->shouldReceive('isDirectory')->andReturn(true);

        // $this->assertEquals('fa-image',    (new LfmItem($path1))->icon());
        // $this->assertEquals('fa-folder-o', (new LfmItem($path3))->icon());
    }

    public function testHasThumb()
    {
        $this->lfm_path->shouldReceive('mimeType')->andReturn('application/plain')->shouldReceive('isDirectory')
            ->andReturn(false);

        $item = new LfmItem($this->lfm_path, $this->lfm);

        $this->assertFalse($item->hasThumb());
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
