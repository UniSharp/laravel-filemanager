<?php

namespace Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Unisharp\Laravelfilemanager\LfmItem;
use Unisharp\Laravelfilemanager\LfmStorage;
use Unisharp\Laravelfilemanager\Lfm;
use Unisharp\Laravelfilemanager\LfmPath;

class LfmStorageTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testDirectories()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('directories')->with('foo')->andReturn(['bar']);

        $lfm_path = m::mock(LfmPath::class);
        $lfm_path->shouldReceive('path')->with('storage')->andReturn('foo');

        $storage = new LfmStorage($lfm_path, $disk);

        $this->assertEquals('bar', $storage->directories('foo')[0]);
    }

    public function testFiles()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('files')->with('foo')->andReturn(['bar']);

        $lfm_path = m::mock(LfmPath::class);
        $lfm_path->shouldReceive('path')->with('storage')->andReturn('foo');

        $storage = new LfmStorage($lfm_path, $disk);

        $this->assertEquals('bar', $storage->files('foo')[0]);
    }

    public function testMakeDirectory()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('makeDirectory')->with('foo', 0777, true, true)->andReturn(true);

        $lfm_path = m::mock(LfmPath::class);
        $lfm_path->shouldReceive('path')->with('storage')->andReturn('foo');

        $storage = new LfmStorage($lfm_path, $disk);

        $this->assertTrue($storage->makeDirectory('foo'));
    }

    public function testExists()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('exists')->with('foo')->andReturn(true);

        $lfm_path = m::mock(LfmPath::class);
        $lfm_path->shouldReceive('path')->with('storage')->andReturn('foo');

        $storage = new LfmStorage($lfm_path, $disk);

        $this->assertTrue($storage->exists('foo'));
    }

    public function testGetFile()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('get')->with('foo')->andReturn(true);

        $lfm_path = m::mock(LfmPath::class);
        $lfm_path->shouldReceive('path')->with('storage')->andReturn('foo');

        $storage = new LfmStorage($lfm_path, $disk);

        $this->assertTrue($storage->getFile('foo'));
    }

    public function testMimeType()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('mimeType')->with('foo')->andReturn('text/plain');

        $lfm_path = m::mock(LfmPath::class);
        $lfm_path->shouldReceive('path')->with('storage')->andReturn('foo');

        $storage = new LfmStorage($lfm_path, $disk);

        $this->assertEquals('text/plain', $storage->mimeType('foo'));
    }

    public function testIsDirectory()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('directories')->with('foo')->andReturn(['foo/bar']);

        $lfm_path = m::mock(LfmPath::class);
        $lfm_path->shouldReceive('path')->with('storage')->andReturn('foo/bar');

        $storage = new LfmStorage($lfm_path, $disk);

        $this->assertTrue($storage->isDirectory());
    }
}
