<?php

namespace Tests;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Http\Request;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use UniSharp\LaravelFilemanager\Lfm;
use UniSharp\LaravelFilemanager\LfmFileRepository;
use UniSharp\LaravelFilemanager\LfmStorageRepository;

class LfmTest extends TestCase
{
    public function tearDown(): void
    {
        m::close();

        parent::tearDown();
    }

    public function testGetStorage()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('lfm.disk')->once()->andReturn('local');

        $lfm = new Lfm($config);
        $this->assertInstanceOf(LfmStorageRepository::class, $lfm->getStorage('foo/bar'));
    }

    public function testInput()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('input')->with('foo')->andReturn('bar');

        $lfm = new Lfm(m::mock(Config::class), $request);

        $this->assertEquals('bar', $lfm->input('foo'));
    }

    public function testGetNameFromPath()
    {
        $this->assertEquals('bar', (new Lfm)->getNameFromPath('foo/bar'));
    }

    public function testAllowFolderType()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('lfm.allow_private_folder')->once()->andReturn(true);
        $config->shouldReceive('get')->with('lfm.allow_private_folder')->once()->andReturn(false);
        $config->shouldReceive('get')->with('lfm.allow_private_folder')->once()->andReturn(true);
        $config->shouldReceive('get')->with('lfm.allow_shared_folder')->once()->andReturn(false);
        $config->shouldReceive('get')->with('lfm.folder_categories')->andReturn([]);
        $config->shouldReceive('has')->andReturn(false);

        $request = m::mock(Request::class);
        $request->shouldReceive('input')->with('type')->andReturn('');

        $lfm = new Lfm($config, $request);

        $this->assertTrue($lfm->allowFolderType('user'));
        $this->assertTrue($lfm->allowFolderType('shared'));
        $this->assertFalse($lfm->allowFolderType('shared'));
    }

    public function testGetCategoryName()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')
               ->with('lfm.folder_categories.file.folder_name', m::type('string'))
               ->once()
               ->andReturn('files');
        $config->shouldReceive('get')
               ->with('lfm.folder_categories.image.folder_name', m::type('string'))
               ->once()
               ->andReturn('photos');
        $config->shouldReceive('get')
            ->with('lfm.folder_categories')
            ->andReturn(['file' => [], 'image' => []]);

        $request = m::mock(Request::class);
        $request->shouldReceive('input')->with('type')->once()->andReturn('file');
        $request->shouldReceive('input')->with('type')->once()->andReturn('image');

        $lfm = new Lfm($config, $request);

        $this->assertEquals('files', $lfm->getCategoryName('file'));
        $this->assertEquals('photos', $lfm->getCategoryName('image'));
    }

    public function testCurrentLfmType()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('input')->with('type')->once()->andReturn('file');
        $request->shouldReceive('input')->with('type')->once()->andReturn('image');
        $request->shouldReceive('input')->with('type')->once()->andReturn('foo');

        $config = m::mock(Config::class);
        $config->shouldReceive('get')
            ->with('lfm.folder_categories')
            ->andReturn(['file' => [], 'image' => []]);

        $lfm = new Lfm($config, $request);

        $this->assertEquals('file', $lfm->currentLfmType());
        $this->assertEquals('image', $lfm->currentLfmType());
        $this->assertEquals('file', $lfm->currentLfmType());
    }

    public function testGetUserSlug()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('lfm.private_folder_name')->once()->andReturn(function () {
            return 'foo';
        });

        $lfm = new Lfm($config);

        $this->assertEquals('foo', $lfm->getUserSlug());
    }

    public function testGetRootFolder()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('lfm.allow_private_folder')->andReturn(true);
        $config->shouldReceive('get')->with('lfm.private_folder_name')->once()->andReturn(function () {
            return 'foo';
        });
        $config->shouldReceive('get')->with('lfm.shared_folder_name')->once()->andReturn('bar');

        $lfm = new Lfm($config);

        $this->assertEquals('/foo', $lfm->getRootFolder('user'));
        $this->assertEquals('/bar', $lfm->getRootFolder('shared'));
    }

    public function testGetThumbFolderName()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('lfm.thumb_folder_name')->once()->andReturn('foo');

        $lfm = new Lfm($config);

        $this->assertEquals('foo', $lfm->getThumbFolderName());
    }

    public function testGetFileType()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('lfm.file_type_array.foo', m::type('string'))->once()->andReturn('foo');
        $config->shouldReceive('get')->with(m::type('string'), m::type('string'))->once()->andReturn('File');

        $lfm = new Lfm($config);

        $this->assertEquals('foo', $lfm->getFileType('foo'));
        $this->assertEquals('File', $lfm->getFileType('bar'));
    }

    public function testAllowMultiUser()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('lfm.allow_private_folder')->once()->andReturn(true);
        $config->shouldReceive('get')->with('lfm.folder_categories')->andReturn([]);
        $config->shouldReceive('has')->andReturn(false);

        $request = m::mock(Request::class);
        $request->shouldReceive('input')->with('type')->andReturn('');

        $lfm = new Lfm($config, $request);

        $this->assertTrue($lfm->allowMultiUser());
    }

    public function testAllowShareFolder()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('lfm.allow_private_folder')->once()->andReturn(false);
        $config->shouldReceive('get')->with('lfm.allow_private_folder')->once()->andReturn(true);
        $config->shouldReceive('get')->with('lfm.allow_shared_folder')->once()->andReturn(false);
        $config->shouldReceive('get')->with('lfm.folder_categories')->andReturn([]);
        $config->shouldReceive('has')->andReturn(false);

        $request = m::mock(Request::class);
        $request->shouldReceive('input')->with('type')->andReturn('');

        $lfm = new Lfm($config, $request);

        $this->assertTrue($lfm->allowShareFolder());
        $this->assertFalse($lfm->allowShareFolder());
    }

    public function testTranslateFromUtf8()
    {
        $input = 'test/測試';

        $this->assertEquals($input, (new Lfm)->translateFromUtf8($input));
    }
}
