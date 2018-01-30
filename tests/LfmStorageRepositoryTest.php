<?php

namespace Tests;

use Illuminate\Support\Facades\Storage;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use UniSharp\LaravelFilemanager\Lfm;
use UniSharp\LaravelFilemanager\LfmPath;
use UniSharp\LaravelFilemanager\LfmStorageRepository;

class LfmStorageRepositoryTest extends TestCase
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
        $disk->shouldReceive('directories')->with('foo')->andReturn(['foo/bar']);
        $disk->shouldReceive('move')->with('foo/bar', 'foo/bar/baz')->andReturn(true);

        $helper = m::mock(Lfm::class);
        $helper->shouldReceive('config')->with('disk')->andReturn('local');

        Storage::shouldReceive('disk')->with('local')->andReturn($disk);

        $this->storage = new LfmStorageRepository('foo/bar', $helper);
    }

    public function tearDown()
    {
        m::close();
    }

    public function testMagicCall()
    {
        $this->assertEquals('baz', $this->storage->functionToCall());
    }

    public function testRootPath()
    {
        $this->assertEquals('foo/bar', $this->storage->rootPath());
    }

    public function testMove()
    {
        $new_lfm_path = m::mock(LfmPath::class);
        $new_lfm_path->shouldReceive('path')->with('storage')->andReturn('foo/bar/baz');

        $this->assertTrue($this->storage->move($new_lfm_path));
    }
}
