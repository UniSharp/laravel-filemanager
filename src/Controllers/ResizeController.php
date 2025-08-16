<?php

namespace UniSharp\LaravelFilemanager\Controllers;

use UniSharp\LaravelFilemanager\Services\ImageService;
use UniSharp\LaravelFilemanager\Events\ImageIsResizing;
use UniSharp\LaravelFilemanager\Events\ImageWasResized;

class ResizeController extends LfmController
{
    private ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
        parent::__construct();
    }

    /**
     * Dipsplay image for resizing.
     *
     * @return mixed
     */
    public function getResize()
    {
        $ratio = 1.0;
        $image = request('img');

        $original_image = $this->imageService->read($this->lfm->setName($image)->path('absolute'));
        $original_width = $original_image->width();
        $original_height = $original_image->height();

        $scaled = false;

        // FIXME size should be configurable
        if ($original_width > 600) {
            $ratio = 600 / $original_width;
            $width = $original_width * $ratio;
            $height = $original_height * $ratio;
            $scaled = true;
        } else {
            $width = $original_width;
            $height = $original_height;
        }

        if ($height > 400) {
            $ratio = 400 / $original_height;
            $width = $original_width * $ratio;
            $height = $original_height * $ratio;
            $scaled = true;
        }

        return view('laravel-filemanager::resize')
            ->with('img', $this->lfm->pretty($image))
            ->with('height', number_format($height, 0))
            ->with('width', $width)
            ->with('original_height', $original_height)
            ->with('original_width', $original_width)
            ->with('scaled', $scaled)
            ->with('ratio', $ratio);
    }

    public function performResize($overWrite = true)
    {
        $image_name = request('img');
        $image_path = $this->lfm->setName(request('img'))->path('absolute');
        $resize_path = $image_path;

        if (! $overWrite) {
            $fileParts = explode('.', $image_name);
            $fileParts[count($fileParts) - 2] = $fileParts[count($fileParts) - 2] . '_resized_' . time();
            $resize_path = $this->lfm->setName(implode('.', $fileParts))->path('absolute');
        }

        event(new ImageIsResizing($image_path));

        $this->imageService->read($image_path)
            ->resize(request('dataWidth'), request('dataHeight'))
            ->save($resize_path);

        event(new ImageWasResized($image_path));

        return parent::$success_response;
    }

    public function performResizeNew()
    {
        $this->performResize(false);
    }
}
