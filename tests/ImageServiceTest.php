<?php

namespace Tests;

use Intervention\Image\Interfaces\ImageManagerInterface;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use UniSharp\LaravelFilemanager\Services\ImageService;

class ImageServiceTest extends TestCase
{
    public function tearDown(): void
    {
        m::close();

        parent::tearDown();
    }

    public function testOptimizesUploadWithConfiguredDimensions()
    {
        $manager = m::mock(ImageManagerInterface::class);
        $image = m::mock();

        $manager->shouldReceive('read')->with('contents')->once()->andReturn($image);
        $image->shouldReceive('scaleDown')->with(1200, 900)->once()->andReturn($image);
        $image->shouldReceive('encodeByMediaType')->once()->andReturn('encoded');

        $service = new ImageService($manager);

        $this->assertSame('encoded', $service->optimizeUpload('contents', 'image/pjpeg', [
            'quality' => 80,
            'max_width' => 1200,
            'max_height' => 900,
        ]));
    }

    public function testOptimizesUploadWithoutResizing()
    {
        $manager = m::mock(ImageManagerInterface::class);
        $image = m::mock();

        $manager->shouldReceive('read')->with('contents')->once()->andReturn($image);
        $image->shouldNotReceive('scaleDown');
        $image->shouldReceive('encodeByMediaType')->once()->andReturn('encoded');

        $service = new ImageService($manager);

        $this->assertSame('encoded', $service->optimizeUpload('contents', 'image/jpeg', [
            'quality' => 80,
        ]));
    }
}
