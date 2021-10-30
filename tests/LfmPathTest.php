<?php

namespace Tests;

use Illuminate\Http\Request;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use UniSharp\LaravelFilemanager\Lfm;
use UniSharp\LaravelFilemanager\LfmItem;
use UniSharp\LaravelFilemanager\LfmPath;

class LfmPathTest extends TestCase
{
    public function tearDown()
    {
        m::close();

        parent::tearDown();
    }

    public function testMagicGet()
    {
        $storage = m::mock(LfmStorage::class);

        $helper = m::mock(Lfm::class);
        $helper->shouldReceive('getStorage')->with('files/bar')->andReturn($storage);
        $helper->shouldReceive('getCategoryName')->andReturn('files');
        $helper->shouldReceive('input')->with('working_dir')->andReturn('/bar');
        $helper->shouldReceive('isRunningOnWindows')->andReturn(false);
        $helper->shouldReceive('ds')->andReturn('/');

        $path = new LfmPath($helper);

        $this->assertEquals($storage, $path->storage);
    }

    public function testMagicCall()
    {
        $storage = m::mock(LfmStorage::class);
        $storage->shouldReceive('foo')->andReturn('bar');

        $helper = m::mock(Lfm::class);
        $helper->shouldReceive('getStorage')->with('files/bar')->andReturn($storage);
        $helper->shouldReceive('getCategoryName')->andReturn('files');
        $helper->shouldReceive('input')->with('working_dir')->andReturn('/bar');
        $helper->shouldReceive('isRunningOnWindows')->andReturn(false);
        $helper->shouldReceive('ds')->andReturn('/');

        $path = new LfmPath($helper);

        $this->assertEquals('bar', $path->foo());
    }

    public function testDirAndNormalizeWorkingDir()
    {
        $helper = m::mock(Lfm::class);
        $helper->shouldReceive('input')->with('working_dir')->once()->andReturn('foo');
        $helper->shouldReceive('isRunningOnWindows')->andReturn(false);

        $path = new LfmPath($helper);

        $this->assertEquals('foo', $path->normalizeWorkingDir());
        $this->assertEquals('bar', $path->dir('bar')->normalizeWorkingDir());
    }

    public function testSetNameAndGetName()
    {
        $path = new LfmPath(m::mock(Lfm::class));

        $path->setName('bar');

        $this->assertEquals('bar', $path->getName());
    }

    public function testPath()
    {
        $helper = m::mock(Lfm::class);
        $helper->shouldReceive('getRootFolder')->andReturn('/foo');
        $helper->shouldReceive('basePath')->andReturn(realpath(__DIR__ . '/../'));
        $helper->shouldReceive('input')->with('working_dir')->andReturnNull();
        $helper->shouldReceive('getCategoryName')->andReturn('files');
        $helper->shouldReceive('isRunningOnWindows')->andReturn(false);
        $helper->shouldReceive('ds')->andReturn('/');

        $storage = m::mock(LfmStorage::class);
        $storage->shouldReceive('rootPath')->andReturn(realpath(__DIR__ . '/../') . '/storage/app');

        $helper->shouldReceive('getStorage')->andReturn($storage);

        $path = new LfmPath($helper);

        $this->assertEquals('files/foo', $path->path());
        $this->assertEquals('files/foo/bar', $path->setName('bar')->path('storage'));
    }

    public function testUrl()
    {
        $helper = m::mock(Lfm::class);
        $helper->shouldReceive('getRootFolder')->andReturn('/foo');
        $helper->shouldReceive('input')->with('working_dir')->andReturnNull();
        $helper->shouldReceive('getCategoryName')->andReturn('files');
        $helper->shouldReceive('isRunningOnWindows')->andReturn(false);
        $helper->shouldReceive('ds')->andReturn('/');

        $storage = m::mock(LfmStorage::class);
        $storage->shouldReceive('url')->andReturn('/files/foo/foo');

        $helper->shouldReceive('getStorage')->andReturn($storage);

        $path = new LfmPath($helper);

        $this->assertEquals('/files/foo/foo', $path->setName('foo')->url());
    }

    public function testFolders()
    {
        $storage = m::mock(LfmStorage::class);
        $storage->shouldReceive('directories')->andReturn(['foo/bar']);

        $helper = m::mock(Lfm::class);
        $helper->shouldReceive('getCategoryName')->andReturn('files');
        $helper->shouldReceive('input')->with('working_dir')->andReturn('/shares');
        $helper->shouldReceive('input')->with('sort_type')->andReturn('alphabetic');
        $helper->shouldReceive('getStorage')->andReturn($storage);
        $helper->shouldReceive('getNameFromPath')->andReturn('bar');
        $helper->shouldReceive('getThumbFolderName')->andReturn('thumbs');
        $helper->shouldReceive('isRunningOnWindows')->andReturn(false);
        $helper->shouldReceive('ds')->andReturn('/');
        $helper->shouldReceive('config')
            ->with('item_columns')
            ->andReturn(['name', 'url', 'time', 'icon', 'is_file', 'is_image', 'thumb_url']);

        $path = new LfmPath($helper);

        $this->assertInstanceOf(LfmItem::class, $path->folders()[0]);
    }

    public function testFiles()
    {
        $storage = m::mock(LfmStorage::class);
        $storage->shouldReceive('files')->andReturn(['foo/bar']);

        $helper = m::mock(Lfm::class);
        $helper->shouldReceive('getCategoryName')->andReturn('files');
        $helper->shouldReceive('input')->with('working_dir')->andReturn('/shares');
        $helper->shouldReceive('input')->with('sort_type')->andReturn('alphabetic');
        $helper->shouldReceive('getStorage')->andReturn($storage);
        $helper->shouldReceive('getNameFromPath')->andReturn('bar');
        $helper->shouldReceive('isRunningOnWindows')->andReturn(false);
        $helper->shouldReceive('ds')->andReturn('/');
        $helper->shouldReceive('config')
            ->with('item_columns')
            ->andReturn(['name', 'url', 'time', 'icon', 'is_file', 'is_image', 'thumb_url']);

        $path = new LfmPath($helper);

        $this->assertInstanceOf(LfmItem::class, $path->files()[0]);
    }

    public function testPretty()
    {
        $helper = m::mock(Lfm::class);
        $helper->shouldReceive('getNameFromPath')->andReturn('bar');
        $helper->shouldReceive('isRunningOnWindows')->andReturn(false);
        $helper->shouldReceive('config')
            ->with('item_columns')
            ->andReturn(['name', 'url', 'time', 'icon', 'is_file', 'is_image', 'thumb_url']);

        $path = new LfmPath($helper);

        $this->assertInstanceOf(LfmItem::class, $path->pretty('foo'));
    }

    public function testCreateFolder()
    {
        $storage = m::mock(LfmStorage::class);
        $storage->shouldReceive('rootPath')->andReturn(realpath(__DIR__ . '/../') . '/storage/app');
        $storage->shouldReceive('exists')->andReturn(false);
        $storage->shouldReceive('makeDirectory')->andReturn(true);

        $helper = m::mock(Lfm::class);
        $helper->shouldReceive('getStorage')->with('files/bar')->andReturn($storage);
        $helper->shouldReceive('getCategoryName')->andReturn('files');
        $helper->shouldReceive('input')->with('working_dir')->andReturn('/bar');
        $helper->shouldReceive('isRunningOnWindows')->andReturn(false);
        $helper->shouldReceive('ds')->andReturn('/');

        $path = new LfmPath($helper);

        $this->assertNull($path->createFolder('bar'));
    }

    public function testCreateFolderButFolderAlreadyExists()
    {
        $storage = m::mock(LfmStorage::class);
        $storage->shouldReceive('exists')->andReturn(true);
        $storage->shouldReceive('makeDirectory')->andReturn(true);

        $helper = m::mock(Lfm::class);
        $helper->shouldReceive('getStorage')->with('files/bar')->andReturn($storage);
        $helper->shouldReceive('getCategoryName')->andReturn('files');
        $helper->shouldReceive('input')->with('working_dir')->andReturn('/bar');
        $helper->shouldReceive('isRunningOnWindows')->andReturn(false);
        $helper->shouldReceive('ds')->andReturn('/');

        $path = new LfmPath($helper);

        $this->assertFalse($path->createFolder('foo'));
    }
}
