<?php

namespace Tests;

use Illuminate\Support\Facades\Storage;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use UniSharp\LaravelFilemanager\LfmPath;
use UniSharp\LaravelFilemanager\LfmStorage;

class LfmStorageTest extends TestCase
{
    private $storage;

    public function setUp()
    {
        parent::setUp();

        $disk = m::mock('disk');
        $disk->shouldReceive('getDriver')->andReturn($disk);
        $disk->shouldReceive('getAdapter')->andReturn($disk);
        $disk->shouldReceive('getPathPrefix')->andReturn('foo/bar');
        $disk->shouldReceive('functionToCall')->with('foo/bar')->andReturn('baz');
        $disk->shouldReceive('directories')->with('foo/bar')->andReturn(['foo/bar/baz']);
        $disk->shouldReceive('files')->with('foo/bar')->andReturn(['foo/bar/baz']);
        $disk->shouldReceive('makeDirectory')->with('foo/bar', 0777, true, true)->andReturn(true);
        $disk->shouldReceive('exists')->with('foo/bar')->andReturn(true);
        $disk->shouldReceive('get')->with('foo/bar')->andReturn(true);
        $disk->shouldReceive('mimeType')->with('foo/bar')->andReturn('text/plain');
        $disk->shouldReceive('directories')->with('foo')->andReturn(['foo/bar']);

        $disk->shouldReceive('move')->with('foo/bar', 'foo/bar/baz')->andReturn(true);
        $disk->shouldReceive('allFiles')->with('foo/bar')->andReturn([]);

        Storage::shouldReceive('disk')->andReturn($disk);

        $this->storage = new LfmStorage('foo/bar');
    }

    public function tearDown()
    {
        m::close();
    }

    public function test__Call()
    {
        $this->assertEquals('baz', $this->storage->functionToCall());
    }

    public function testRootPath()
    {
        $this->assertEquals('foo/bar', $this->storage->rootPath());
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

    public function testMove()
    {
        $new_lfm_path = m::mock(LfmPath::class);
        $new_lfm_path->shouldReceive('path')->with('storage')->andReturn('foo/bar/baz');

        $this->assertTrue($this->storage->move($new_lfm_path));
    }

    public function testDirectoryIsEmpty()
    {
        $this->assertTrue($this->storage->directoryIsEmpty());
    }
}
