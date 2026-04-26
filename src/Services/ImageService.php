<?php

namespace UniSharp\LaravelFilemanager\Services;

use Intervention\Image\Interfaces\ImageManagerInterface;

class ImageService
{
    protected ImageManagerInterface $imageManager;

    public function __construct(ImageManagerInterface $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    public function optimizeUpload(string $contents, string $mimeType, array $options)
    {
        $image = $this->imageManager->read($contents);

        $maxWidth = $this->dimension($options['max_width'] ?? null);
        $maxHeight = $this->dimension($options['max_height'] ?? null);

        if ($maxWidth || $maxHeight) {
            $image->scaleDown($maxWidth, $maxHeight);
        }

        $format = $options['format'] ?? null;
        $mimeType = $this->outputMimeType($mimeType, is_string($format) ? $format : null);
        $quality = $this->quality($options['quality'] ?? 85);

        if ($mimeType === 'image/jpeg') {
            return $image->encodeByMediaType(
                $mimeType,
                progressive: (bool) ($options['progressive'] ?? true),
                quality: $quality
            );
        }

        if (in_array($mimeType, ['image/webp', 'image/avif'])) {
            return $image->encodeByMediaType($mimeType, quality: $quality);
        }

        return $image->encodeByMediaType($mimeType);
    }

    private function dimension($value): ?int
    {
        $value = (int) $value;

        return $value > 0 ? $value : null;
    }

    private function quality($value): int
    {
        if (!is_numeric($value)) {
            return 85;
        }

        return max(0, min(100, (int) $value));
    }

    public function outputMimeType(string $mimeType, ?string $format = null): string
    {
        if (is_string($format) && $format !== '') {
            return match (strtolower($format)) {
                'jpg', 'jpeg', 'image/jpeg' => 'image/jpeg',
                'png', 'image/png' => 'image/png',
                'webp', 'image/webp' => 'image/webp',
                'avif', 'image/avif' => 'image/avif',
                'gif', 'image/gif' => 'image/gif',
                'bmp', 'bitmap', 'image/bmp' => 'image/bmp',
                'tif', 'tiff', 'image/tiff' => 'image/tiff',
                'jp2', 'jpx', 'jpeg2000', 'jpeg 2000', 'image/jp2' => 'image/jp2',
                'heic', 'image/heic' => 'image/heic',
                default => $this->normalizeMimeType($mimeType),
            };
        }

        return $this->normalizeMimeType($mimeType);
    }

    public function extensionForMimeType(string $mimeType): string
    {
        return match ($this->normalizeMimeType($mimeType)) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/avif' => 'avif',
            'image/gif' => 'gif',
            'image/bmp' => 'bmp',
            'image/tiff' => 'tiff',
            'image/jp2' => 'jp2',
            'image/heic' => 'heic',
            default => '',
        };
    }

    private function normalizeMimeType(string $mimeType): string
    {
        return $mimeType === 'image/pjpeg' ? 'image/jpeg' : $mimeType;
    }

    /**
     * Dynamically forward method calls to the underlying ImageManagerInterface.
     */
    public function __call(string $method, array $arguments)
    {
        if (method_exists($this->imageManager, $method)) {
            return $this->imageManager->$method(...$arguments);
        }

        throw new \BadMethodCallException("Method {$method} does not exist on ImageService or ImageManagerInterface.");
    }
}
