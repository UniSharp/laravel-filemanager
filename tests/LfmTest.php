<?php

namespace Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use UniSharp\LaravelFilemanager\Lfm;
use Unisharp\Laravelfilemanager\LfmStorage;
use Illuminate\Contracts\Config\Repository as Config;

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
}
