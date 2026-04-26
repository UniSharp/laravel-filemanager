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

    public function testCanConvertUploadsToConfiguredFormat()
    {
        $manager = m::mock(ImageManagerInterface::class);
        $image = m::mock();

        $manager->shouldReceive('read')->with('contents')->once()->andReturn($image);
        $image->shouldNotReceive('scaleDown');
        $image->shouldReceive('encodeByMediaType')->once()->andReturn('encoded');

        $service = new ImageService($manager);

        $this->assertSame('encoded', $service->optimizeUpload('contents', 'image/png', [
            'format' => 'avif',
            'quality' => 80,
        ]));
        $this->assertSame('image/avif', $service->outputMimeType('image/png', 'avif'));
        $this->assertSame('webp', $service->extensionForMimeType('image/webp'));
    }

    public function testRecognizesConfiguredOutputFormats()
    {
        $service = new ImageService(m::mock(ImageManagerInterface::class));

        $this->assertSame('image/jpeg', $service->outputMimeType('image/png', 'jpg'));
        $this->assertSame('image/png', $service->outputMimeType('image/jpeg', 'png'));
        $this->assertSame('image/webp', $service->outputMimeType('image/jpeg', 'webp'));
        $this->assertSame('image/avif', $service->outputMimeType('image/jpeg', 'avif'));
        $this->assertSame('image/gif', $service->outputMimeType('image/jpeg', 'gif'));
        $this->assertSame('image/bmp', $service->outputMimeType('image/jpeg', 'bmp'));
        $this->assertSame('image/tiff', $service->outputMimeType('image/jpeg', 'tiff'));
        $this->assertSame('image/jp2', $service->outputMimeType('image/jpeg', 'jp2'));
        $this->assertSame('image/heic', $service->outputMimeType('image/jpeg', 'heic'));
    }
}
