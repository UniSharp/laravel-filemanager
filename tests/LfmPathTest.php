<?php

namespace Tests;

use Mockery as m;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use UniSharp\LaravelFilemanager\Lfm;
use UniSharp\LaravelFilemanager\LfmPath;

class LfmPathTest extends TestCase
{
    public function tearDown()
    {
        m::close();

        parent::tearDown();
    }

    public function testNormalizeWorkingDir()
    {
        $helper = m::mock(Lfm::class);
        $helper->shouldReceive('input')->with('working_dir')->once()->andReturn('foo');

        $path = new LfmPath($helper);

        $this->assertEquals('foo', $path->normalizeWorkingDir());
        $this->assertEquals('bar', $path->dir('bar')->normalizeWorkingDir());
    }

    // public function testGetPathPrefix()
    // {
    //     $helper = m::mock(Lfm::class);
    //     $helper->shouldReceive('getCategoryName')->with('image')->once()->andReturn('photos');
    //     $helper->shouldReceive('getCategoryName')->with('file')->once()->andReturn('files');
    //     $helper->shouldReceive('basePath')->andReturn(realpath(__DIR__ . '/../'));
    //     $helper->shouldReceive('getDiskRoot')->andReturn('storage/app');

    //     $request = m::mock(Request::class);
    //     $helper->shouldReceive('isProcessingImages')->once()->andReturn(true);
    //     $helper->shouldReceive('isProcessingImages')->once()->andReturn(false);

    //     $path = new LfmPath($helper, $request);

    //     $this->assertEquals('storage/app/laravel-filemanager/photos', $path->getPathPrefix());
    //     $this->assertEquals('storage/app/laravel-filemanager/files', $path->getPathPrefix());
    // }

    public function testPath()
    {
        $helper = m::mock(Lfm::class);
        $helper->shouldReceive('allowFolderType')->with('user')->andReturn(true);
        $helper->shouldReceive('getRootFolder')->with('user')->andReturn('/foo');
        $helper->shouldReceive('basePath')->andReturn(realpath(__DIR__ . '/../'));
        $helper->shouldReceive('getCategoryName')->with('file')->times(2)->andReturn('files');

        $helper->shouldReceive('isProcessingImages')->times(2)->andReturn(false);
        $helper->shouldReceive('input')->with('working_dir')->andReturnNull();

        $storage = m::mock(LfmStorage::class);
        $storage->shouldReceive('rootPath')->andReturn(realpath(__DIR__ . '/../') . '/storage/app');

        $helper->shouldReceive('getStorage')->andReturn($storage);

        $path = new LfmPath($helper);

        $this->assertEquals('laravel-filemanager/files/foo', $path->path());
        $this->assertEquals('laravel-filemanager/files/foo/bar', $path->setName('bar')->path('storage'));
    }

    public function testUrl()
    {
        $helper = m::mock(Lfm::class);
        $helper->shouldReceive('getUrlPrefix')->once()->andReturn('laravel-filemanager');
        $helper->shouldReceive('getCategoryName')->with('file')->once()->andReturn('files');
        $helper->shouldReceive('allowFolderType')->with('user')->once()->andReturn(true);
        $helper->shouldReceive('getRootFolder')->with('user')->once()->andReturn('/foo');
        $helper->shouldReceive('url')->with(m::type('string'))->once()->andReturnUsing(function ($path) {
            return "http://localhost/{$path}";
        });
        $helper->shouldReceive('isProcessingImages')->times(2)->andReturn(false);
        $helper->shouldReceive('input')->with('working_dir')->once()->andReturnNull();

        $path = new LfmPath($helper);

        $this->assertEquals('http://localhost/laravel-filemanager/files/foo/foo', $path->setName('foo')->url());
    }

    // public function testFolders()
    // {
    //     $storage = m::mock('storage');
    //     $storage->disk_root = realpath(__DIR__ . '/../') . '/storage/app';
    //     $storage->shouldReceive('directories')
    //         ->with('laravel-filemanager/files/foo')
    //         ->once()
    //         ->andReturn([
    //             $directory = (object) ['name' => 'foo'],
    //             (object) ['name' => 'thumbs'],
    //         ]);

    //     $lfm = m::mock(Lfm::class);
    //     $lfm->shouldReceive('allowFolderType')->with('user')->once()->andReturn(true);
    //     $lfm->shouldReceive('getRootFolder')->with('user')->once()->andReturn('/foo');
    //     $lfm->shouldReceive('basePath')->andReturn(realpath(__DIR__ . '/../'));
    //     $lfm->shouldReceive('getCategoryName')->with('file')->once()->andReturn('files');
    //     $lfm->shouldReceive('getStorage')->andReturn($storage);
    //     $lfm->shouldReceive('getThumbFolderName')->andReturn('thumbs');

    //     $request = m::mock(Request::class);
    //     $request->shouldReceive('input')->with('type')->once()->andReturn('file');
    //     $request->shouldReceive('input')->with('working_dir')->once()->andReturnNull();

    //     $path = new LfmPath($lfm, $request);

    //     $this->assertEquals([$directory], $path->folders());
    // }

    // public function testFiles()
    // {
    //     $storage = m::mock('storage');
    //     $storage->disk_root = realpath(__DIR__ . '/../') . '/storage/app';
    //     $storage->shouldReceive('files')
    //         ->with('laravel-filemanager/files/foo')
    //         ->once()
    //         ->andReturn(['file']);

    //     $lfm = m::mock(Lfm::class);
    //     $lfm->shouldReceive('allowFolderType')->with('user')->once()->andReturn(true);
    //     $lfm->shouldReceive('getRootFolder')->with('user')->once()->andReturn('/foo');
    //     $lfm->shouldReceive('basePath')->andReturn(realpath(__DIR__ . '/../'));
    //     $lfm->shouldReceive('getCategoryName')->with('file')->once()->andReturn('files');
    //     $lfm->shouldReceive('getStorage')->andReturn($storage);

    //     $request = m::mock(Request::class);
    //     $request->shouldReceive('input')->with('type')->once()->andReturn('file');
    //     $request->shouldReceive('input')->with('working_dir')->once()->andReturnNull();

    //     $path = new LfmPath($lfm, $request);

    //     $this->assertEquals(['file'], $path->files());
    // }

    // public function testFiles()
    // {
    //     $disk = m::mock('disk');
    //     $disk->shouldReceive('files')->with('foo')->once()->andReturn(['foo']);

    //     $storage = new LfmStorage($disk, 'root', m::mock(Lfm::class), m::mock(LfmPath::class));

    //     $this->assertInstanceOf(LfmItem::class, $storage->files('foo')[0]);
    // }

    // public function testExists()
    // {
    //     $storage = m::mock('storage');
    //     $storage->disk_root = realpath(__DIR__ . '/../') . '/storage/app';
    //     $storage->shouldReceive('exists')
    //         ->with('laravel-filemanager/files/foo/bar')
    //         ->once()
    //         ->andReturn(true);

    //     $lfm = m::mock(Lfm::class);
    //     $lfm->shouldReceive('allowFolderType')->with('user')->once()->andReturn(true);
    //     $lfm->shouldReceive('getRootFolder')->with('user')->once()->andReturn('/foo');
    //     $lfm->shouldReceive('basePath')->andReturn(realpath(__DIR__ . '/../'));
    //     $lfm->shouldReceive('getCategoryName')->with('file')->once()->andReturn('files');
    //     $lfm->shouldReceive('getStorage')->andReturn($storage);

    //     $request = m::mock(Request::class);
    //     $request->shouldReceive('input')->with('type')->once()->andReturn('file');
    //     $request->shouldReceive('input')->with('working_dir')->once()->andReturnNull();

    //     $path = new LfmPath($lfm, $request);

    //     $this->assertTrue($path->exists('bar'));
    // }

    // public function testGet()
    // {
    //     $storage = m::mock('storage');
    //     $storage->disk_root = realpath(__DIR__ . '/../') . '/storage/app';
    //     $storage->shouldReceive('get')
    //         ->with('laravel-filemanager/files/foo/bar')
    //         ->once()
    //         ->andReturn('file');

    //     $lfm = m::mock(Lfm::class);
    //     $lfm->shouldReceive('allowFolderType')->with('user')->once()->andReturn(true);
    //     $lfm->shouldReceive('getRootFolder')->with('user')->once()->andReturn('/foo');
    //     $lfm->shouldReceive('basePath')->andReturn(realpath(__DIR__ . '/../'));
    //     $lfm->shouldReceive('getCategoryName')->with('file')->once()->andReturn('files');
    //     $lfm->shouldReceive('getStorage')->andReturn($storage);

    //     $request = m::mock(Request::class);
    //     $request->shouldReceive('input')->with('type')->once()->andReturn('file');
    //     $request->shouldReceive('input')->with('working_dir')->once()->andReturnNull();

    //     $path = new LfmPath($lfm, $request);

    //     $this->assertEquals('file', $path->get('bar'));
    // }

    // public function testCreateFolder()
    // {
    //     $storage = m::mock('storage');
    //     $storage->disk_root = realpath(__DIR__ . '/../') . '/storage/app';
    //     $storage->shouldReceive('createFolder')
    //         ->with('laravel-filemanager/files/foo/bar')
    //         ->once()
    //         ->andReturn(true);

    //     $lfm = m::mock(Lfm::class);
    //     $lfm->shouldReceive('allowFolderType')->with('user')->once()->andReturn(true);
    //     $lfm->shouldReceive('getRootFolder')->with('user')->once()->andReturn('/foo');
    //     $lfm->shouldReceive('basePath')->andReturn(realpath(__DIR__ . '/../'));
    //     $lfm->shouldReceive('getCategoryName')->with('file')->once()->andReturn('files');
    //     $lfm->shouldReceive('getStorage')->andReturn($storage);

    //     $request = m::mock(Request::class);
    //     $request->shouldReceive('input')->with('type')->once()->andReturn('file');
    //     $request->shouldReceive('input')->with('working_dir')->once()->andReturnNull();

    //     $path = new LfmPath($lfm, $request);

    //     $this->assertTrue($path->createFolder('bar'));
    // }

    // public function testCreateFolder()
    // {
    //     $disk = m::mock('disk');
    //     $disk->shouldReceive('exists')->with('foo')->once()->andReturn(false);
    //     $disk->shouldReceive('makeDirectory')->with('foo', 0777, true, true)->once()->andReturn(true);

    //     $storage = new LfmStorage($disk, 'root', m::mock(Lfm::class), m::mock(LfmPath::class));

    //     $this->assertTrue($storage->createFolder('foo'));
    // }

    // public function testCreateFolderButFolderAlreadyExists()
    // {
    //     $disk = m::mock('disk');
    //     $disk->shouldReceive('exists')->with('foo')->once()->andReturn(true);

    //     $storage = new LfmStorage($disk, 'root', m::mock(Lfm::class), m::mock(LfmPath::class));

    //     $this->assertFalse($storage->createFolder('foo'));
    // }
}
