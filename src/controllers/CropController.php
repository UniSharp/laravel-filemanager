<?php

namespace UniSharp\LaravelFilemanager\controllers;

use Intervention\Image\Facades\Image;
use UniSharp\LaravelFilemanager\Events\ImageIsCropping;
use UniSharp\LaravelFilemanager\Events\ImageWasCropped;

class CropController extends LfmController
{
    /**
     * Show crop page.
     *
     * @return mixed
     */
    public function getCrop()
    {
        return view('laravel-filemanager::crop')
            ->with([
                'working_dir' => request('working_dir'),
                'img' => $this->lfm->get(request('img'))
            ]);
    }

    /**
     * Crop the image (called via ajax).
     */
    public function getCropimage($overWrite = true)
    {
        $dataX = request('dataX');
        $dataY = request('dataY');
        $dataHeight = request('dataHeight');
        $dataWidth = request('dataWidth');
        $image_path = $this->lfm->setName(request('img'))->path('absolute');
        $crop_path = $image_path;

        if (! $overWrite) {
            $fileParts = explode('.', request('img'));
            $fileParts[count($fileParts) - 2] = $fileParts[count($fileParts) - 2] . '_cropped_' . time();
            $crop_path = $this->lfm->setName(implode('.', $fileParts))->path('absolute');
        }

        event(new ImageIsCropping($image_path));
        // crop image
        Image::make($image_path)
            ->crop($dataWidth, $dataHeight, $dataX, $dataY)
            ->save($crop_path);

        // make new thumbnail
        $this->lfm->makeThumbnail($this->helper->getNameFromPath($image_path));

        event(new ImageWasCropped($image_path));
    }

    public function getNewCropimage()
    {
        $this->getCropimage(false);
    }
}
