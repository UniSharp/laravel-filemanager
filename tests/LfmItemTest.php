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
        $lfm->shouldReceive('getFileType')->with('baz')->andReturn('File');
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
        $this->lfm_path->shouldReceive('move')->with($this->lfm_path, m::mock(LfmPath::class))->andReturn(true);
        $this->lfm_item = new LfmItem($this->lfm_path, $this->lfm);
    }

    public function tearDown()
    {
        m::close();

        parent::tearDown();
    }

    public function test__Get()
    {
        $this->lfm_item->attributes['foo'] = 'bar';

        $this->assertEquals('bar', $this->lfm_item->foo);
    }

    public function testFill()
    {
        $this->assertEquals(array_keys($this->lfm_item->attributes), array_keys(LfmItem::COLUMNS));
    }

    public function testFileName()
    {
        $this->assertEquals('bar', $this->lfm_item->fileName());
    }

    public function testAbsolutePath()
    {
        $this->assertEquals('foo/bar.baz', $this->lfm_item->absolutePath());
    }

    public function testIsDirectory()
    {
        $this->assertFalse($this->lfm_item->isDirectory());
    }

    public function testIsFile()
    {
        $this->assertTrue($this->lfm_item->isFile());
    }

    public function testIsImage()
    {
        $this->assertFalse($this->lfm_item->isImage());
    }

    public function testMimeType()
    {
        $this->assertEquals('application/plain', $this->lfm_item->mimeType());
    }

    public function testFileType()
    {
        $this->assertEquals('File', $this->lfm_item->fileType());
    }

    public function testExtension()
    {
        $this->assertEquals('baz', $this->lfm_item->extension());
    }

    public function testThumbUrl()
    {
        $this->assertNull($this->lfm_item->thumbUrl());
    }

    // TODO: refactor
    public function testPath()
    {
        $this->assertEquals('foo/bar', $this->lfm_item->path());
    }

    public function testSize()
    {
        $this->assertEquals('1.00 kB', $this->lfm_item->size());
    }

    public function testLastModified()
    {
        $this->assertEquals(0, $this->lfm_item->lastModified());
    }

    public function testIcon()
    {
        $this->assertEquals('fa-file', $this->lfm_item->icon());
        return;

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
        $this->assertFalse($this->lfm_item->hasThumb());
    }

    public function testHumanFilesize()
    {
        $item = $this->lfm_item;

        $this->assertEquals('1.00 kB', $item->humanFilesize(1024));
        $this->assertEquals('1.00 MB', $item->humanFilesize(1024 ** 2));
        $this->assertEquals('1.00 GB', $item->humanFilesize(1024 ** 3));
        $this->assertEquals('1.00 TB', $item->humanFilesize(1024 ** 4));
        $this->assertEquals('1.00 PB', $item->humanFilesize(1024 ** 5));
        $this->assertEquals('1.00 EB', $item->humanFilesize(1024 ** 6));
    }
}
