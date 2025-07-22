<?php

namespace UniSharp\LaravelFilemanager\Services;

use Intervention\Image\Interfaces\ImageManagerInterface;
use Intervention\Image\Image;

class ImageService
{
    protected ImageManagerInterface $imageManager;

    public function __construct(ImageManagerInterface $imageManager)
    {
        $this->imageManager = $imageManager;
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
