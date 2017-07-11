<?php

namespace Tests;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Http\Request;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use UniSharp\LaravelFilemanager\Lfm;
use Unisharp\Laravelfilemanager\LfmPath;
use Unisharp\Laravelfilemanager\LfmStorage;

class LfmTest extends TestCase
{
    public function tearDown()
    {
        m::close();

        parent::tearDown();
    }

    public function testSetAndGetStorage()
    {
        $lfm = new Lfm(m::mock(Config::class));

        $this->assertInstanceOf(Lfm::class, $lfm->setStorage($storage = m::mock(LfmStorage::class)));
        $this->assertEquals($storage, $lfm->getStorage());
    }

    public function testIsProcessingImages()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('input')->with('type')->once()->andReturn('image');
        $request->shouldReceive('input')->with('type')->once()->andReturn('file');

        $lfm = new Lfm(m::mock(Config::class), $request);

        $this->assertTrue($lfm->isProcessingImages());
        $this->assertFalse($lfm->isProcessingImages());
    }

    public function testAllowFolderType()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('lfm.allow_multi_user')->once()->andReturn(true);
        $config->shouldReceive('get')->with('lfm.allow_multi_user')->once()->andReturn(false);
        $config->shouldReceive('get')->with('lfm.allow_multi_user')->once()->andReturn(true);
        $config->shouldReceive('get')->with('lfm.allow_share_folder')->once()->andReturn(false);

        $lfm = new Lfm($config);

        $this->assertTrue($lfm->allowFolderType('user'));
        $this->assertTrue($lfm->allowFolderType('shared'));
        $this->assertFalse($lfm->allowFolderType('shared'));
    }

    public function testGetCategoryName()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')
               ->with('lfm.files_folder_name', m::type('string'))
               ->once()
               ->andReturnUsing($callback = function ($key, $default) {
                   return $default;
               });
        $config->shouldReceive('get')
               ->with('lfm.images_folder_name', m::type('string'))
               ->once()
               ->andReturnUsing($callback);

        $lfm = new Lfm($config);

        $this->assertEquals('files', $lfm->getCategoryName('file'));
        $this->assertEquals('photos', $lfm->getCategoryName('image'));
    }

    public function testGetUserSlug()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('lfm.user_field')->once()->andReturn(function () {
            return 'foo';
        });

        $lfm = new Lfm($config);

        $this->assertEquals('foo', $lfm->getUserSlug());
    }

    public function testGetRootFolder()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('lfm.user_field')->once()->andReturn(function () {
            return 'foo';
        });
        $config->shouldReceive('get')->with('lfm.shared_folder_name')->once()->andReturn('bar');

        $lfm = new Lfm($config);

        $this->assertEquals('/foo', $lfm->getRootFolder('user'));
        $this->assertEquals('/bar', $lfm->getRootFolder('shared'));
    }

    public function testGetUrlPrefix()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('lfm.url_prefix', m::type('string'))->once()->andReturn('foo');
        $config->shouldReceive('get')
               ->with('lfm.url_prefix', m::type('string'))
               ->once()
               ->andReturnUsing(function ($key, $default) {
                   return $default;
               });

        $lfm = new Lfm($config);

        $this->assertEquals('foo', $lfm->getUrlPrefix());
        $this->assertEquals('laravel-filemanager', $lfm->getUrlPrefix());
    }

    public function testGetThumbFolderName()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('lfm.thumb_folder_name')->once()->andReturn('foo');

        $lfm = new Lfm($config);

        $this->assertEquals('foo', $lfm->getThumbFolderName());
    }

    public function testGetFileIcon()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('lfm.file_icon_array.foo', m::type('string'))->once()->andReturn('fa-foo');
        $config->shouldReceive('get')->with(m::type('string'), m::type('string'))->once()->andReturn('fa-file');

        $lfm = new Lfm($config);

        $this->assertEquals('fa-foo', $lfm->getFileIcon('foo'));
        $this->assertEquals('fa-file', $lfm->getFileIcon('bar'));
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
}
