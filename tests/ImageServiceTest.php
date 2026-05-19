<?php

namespace Tests;

use Intervention\Image\ImageManager;
use PHPUnit\Framework\TestCase;
use UniSharp\LaravelFilemanager\Services\ImageService;

class ImageServiceTest extends TestCase
{
    private ImageService $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new ImageService(ImageManager::gd());
    }

    public function testOptimizesUploadWithConfiguredDimensions()
    {
        $encoded = $this->service->optimizeUpload($this->createDetailedJpeg(), 'image/jpeg', [
            'max_width' => 1200,
            'max_height' => 900,
            'quality' => 85,
            'progressive' => false,
        ]);

        $image = $this->service->read((string) $encoded);

        $this->assertSame('image/jpeg', $encoded->mediaType());
        $this->assertSame(1200, $image->width());
        $this->assertSame(720, $image->height());
    }

    public function testCanConvertUploadsToConfiguredFormat()
    {
        $encoded = $this->service->optimizeUpload($this->createDetailedPng(), 'image/png', [
            'format' => 'jpg',
            'quality' => 72,
            'progressive' => true,
        ]);

        $this->assertSame('image/jpeg', $encoded->mediaType());
        $this->assertSame('image/jpeg', $this->service->outputMimeType('image/png', 'jpg'));
        $this->assertSame('webp', $this->service->extensionForMimeType('image/webp'));
        $this->assertJpegUsesProgressiveEncoding((string) $encoded);
    }

    public function testJpegQualityIsClampedToHundred()
    {
        $source = $this->createDetailedJpeg();

        $encoded = $this->service->optimizeUpload($source, 'image/jpeg', [
            'quality' => 100,
            'progressive' => false,
        ]);

        $clamped = $this->service->optimizeUpload($source, 'image/jpeg', [
            'quality' => 140,
            'progressive' => false,
        ]);

        $this->assertSame('image/jpeg', $clamped->mediaType());
        $this->assertSame((string) $encoded, (string) $clamped);
    }

    public function testJpegProgressiveOptionChangesEncodingMode()
    {
        $source = $this->createDetailedJpeg();

        $progressive = $this->service->optimizeUpload($source, 'image/jpeg', [
            'quality' => 80,
            'progressive' => true,
        ]);

        $baseline = $this->service->optimizeUpload($source, 'image/jpeg', [
            'quality' => 80,
            'progressive' => false,
        ]);

        $this->assertJpegUsesProgressiveEncoding((string) $progressive);
        $this->assertJpegUsesBaselineEncoding((string) $baseline);
    }

    public function testPngOptimizationIgnoresQualityAndProgressiveOptions()
    {
        $source = $this->createDetailedPng();

        $encoded = $this->service->optimizeUpload($source, 'image/png', [
            'quality' => 30,
            'progressive' => false,
        ]);

        $withDifferentOptions = $this->service->optimizeUpload($source, 'image/png', [
            'quality' => 90,
            'progressive' => true,
        ]);

        $this->assertSame('image/png', $encoded->mediaType());
        $this->assertSame((string) $encoded, (string) $withDifferentOptions);
    }

    public function testRecognizesConfiguredOutputFormats()
    {
        $this->assertSame('image/jpeg', $this->service->outputMimeType('image/png', 'jpg'));
        $this->assertSame('image/png', $this->service->outputMimeType('image/jpeg', 'png'));
        $this->assertSame('image/webp', $this->service->outputMimeType('image/jpeg', 'webp'));
        $this->assertSame('image/avif', $this->service->outputMimeType('image/jpeg', 'avif'));
        $this->assertSame('image/gif', $this->service->outputMimeType('image/jpeg', 'gif'));
        $this->assertSame('image/bmp', $this->service->outputMimeType('image/jpeg', 'bmp'));
        $this->assertSame('image/tiff', $this->service->outputMimeType('image/jpeg', 'tiff'));
        $this->assertSame('image/jp2', $this->service->outputMimeType('image/jpeg', 'jp2'));
        $this->assertSame('image/heic', $this->service->outputMimeType('image/jpeg', 'heic'));
    }

    private function createDetailedJpeg(int $width = 2000, int $height = 1200): string
    {
        $image = imagecreatetruecolor($width, $height);

        for ($y = 0; $y < $height; $y += 40) {
            for ($x = 0; $x < $width; $x += 40) {
                $color = imagecolorallocate(
                    $image,
                    ($x * 13 + $y * 7) % 256,
                    ($x * 5 + $y * 11) % 256,
                    ($x * 3 + $y * 17) % 256
                );

                imagefilledrectangle($image, $x, $y, min($width - 1, $x + 39), min($height - 1, $y + 39), $color);
            }
        }

        $jpeg = $this->captureImageOutput(static function ($handle): void {
            imagejpeg($handle, null, 95);
        }, $image);

        imagedestroy($image);

        return $jpeg;
    }

    private function createDetailedPng(int $width = 800, int $height = 500): string
    {
        $image = imagecreatetruecolor($width, $height);

        imagealphablending($image, false);
        imagesavealpha($image, true);

        for ($y = 0; $y < $height; $y += 25) {
            for ($x = 0; $x < $width; $x += 25) {
                $color = imagecolorallocatealpha(
                    $image,
                    ($x * 9 + $y * 5) % 256,
                    ($x * 7 + $y * 3) % 256,
                    ($x * 11 + $y * 13) % 256,
                    ($x + $y) % 64
                );

                imagefilledrectangle($image, $x, $y, min($width - 1, $x + 24), min($height - 1, $y + 24), $color);
            }
        }

        $png = $this->captureImageOutput(static function ($handle): void {
            imagepng($handle);
        }, $image);

        imagedestroy($image);

        return $png;
    }

    private function captureImageOutput(callable $encoder, $image): string
    {
        ob_start();
        $encoder($image);

        return (string) ob_get_clean();
    }

    private function assertJpegUsesProgressiveEncoding(string $jpeg): void
    {
        $this->assertNotFalse(strpos($this->jpegHeaders($jpeg), "\xFF\xC2"));
    }

    private function assertJpegUsesBaselineEncoding(string $jpeg): void
    {
        $this->assertNotFalse(strpos($this->jpegHeaders($jpeg), "\xFF\xC0"));
    }

    private function jpegHeaders(string $jpeg): string
    {
        $startOfScan = strpos($jpeg, "\xFF\xDA");

        return $startOfScan === false ? $jpeg : substr($jpeg, 0, $startOfScan);
    }
}
