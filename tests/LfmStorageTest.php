<?php

namespace Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Unisharp\Laravelfilemanager\LfmItem;
use Unisharp\Laravelfilemanager\LfmStorage;

class LfmStorageTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testGet()
    {
        $storage = new LfmStorage(m::mock('disk'), 'root');

        $this->assertInstanceOf(LfmItem::class, $storage->get('foo'));
    }

    public function testExists()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('exists')->with('foo')->once()->andReturn(true);

        $storage = new LfmStorage($disk, 'root');

        $this->assertTrue($storage->exists('foo'));
    }

    public function testCreateFolder()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('exists')->with('foo')->once()->andReturn(false);
        $disk->shouldReceive('makeDirectory')->with('foo', 0777, true, true)->once()->andReturn(true);

        $storage = new LfmStorage($disk, 'root');

        $this->assertTrue($storage->createFolder('foo'));
    }

    public function testCreateFolderButFolderAlreadyExists()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('exists')->with('foo')->once()->andReturn(true);

        $storage = new LfmStorage($disk, 'root');

        $this->assertFalse($storage->createFolder('foo'));
    }

    public function testFiles()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('files')->with('foo')->once()->andReturn(['foo']);

        $storage = new LfmStorage($disk, 'root');

        $this->assertInstanceOf(LfmItem::class, $storage->files('foo')[0]);
    }

    public function testDirectories()
    {
        $disk = m::mock('disk');
        $disk->shouldReceive('directories')->with('foo')->once()->andReturn(['foo']);

        $storage = new LfmStorage($disk, 'root');

        $this->assertInstanceOf(LfmItem::class, $storage->directories('foo')[0]);
    }
}
