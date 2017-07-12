<?php

namespace Tests;

use Illuminate\Support\Facades\Storage;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Unisharp\Laravelfilemanager\LfmPath;
use Unisharp\Laravelfilemanager\LfmStorage;

class LfmStorageTest extends TestCase
{
    private $storage;

    public function setUp()
    {
        parent::setUp();

        $disk = m::mock('disk');
        $disk->shouldReceive('directories')->with('foo/bar')->andReturn(['foo/bar/baz']);
        $disk->shouldReceive('files')->with('foo/bar')->andReturn(['foo/bar/baz']);
        $disk->shouldReceive('makeDirectory')->with('foo/bar', 0777, true, true)->andReturn(true);
        $disk->shouldReceive('exists')->with('foo/bar')->andReturn(true);
        $disk->shouldReceive('get')->with('foo/bar')->andReturn(true);
        $disk->shouldReceive('mimeType')->with('foo/bar')->andReturn('text/plain');
        $disk->shouldReceive('directories')->with('foo')->andReturn(['foo/bar']);

        Storage::shouldReceive('disk')->andReturn($disk);

        $lfm_path = m::mock(LfmPath::class);
        $lfm_path->shouldReceive('path')->with('storage')->andReturn('foo/bar');

        $this->storage = new LfmStorage($lfm_path);
    }

    public function tearDown()
    {
        m::close();
    }

    public function testDirectories()
    {
        $this->assertEquals('foo/bar/baz', $this->storage->directories()[0]);
    }

    public function testFiles()
    {
        $this->assertEquals('foo/bar/baz', $this->storage->files()[0]);
    }

    public function testMakeDirectory()
    {
        $this->assertTrue($this->storage->makeDirectory());
    }

    public function testExists()
    {
        $this->assertTrue($this->storage->exists());
    }

    public function testGetFile()
    {
        $this->assertTrue($this->storage->getFile());
    }

    public function testMimeType()
    {
        $this->assertEquals('text/plain', $this->storage->mimeType());
    }

    public function testIsDirectory()
    {
        $this->assertTrue($this->storage->isDirectory());
    }
}
