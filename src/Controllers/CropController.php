<?php

namespace UniSharp\LaravelFilemanager\Controllers;

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
                'img' => $this->lfm->pretty(request('img'))
            ]);
    }

    /**
     * Crop the image (called via ajax).
     */
    public function getCropimage($overWrite = true)
    {
        $image_name = request('img');
        $image_path = $this->lfm->setName($image_name)->path('absolute');
        $crop_path = $image_path;

        if (! $overWrite) {
            $fileParts = explode('.', $image_name);
            $fileParts[count($fileParts) - 2] = $fileParts[count($fileParts) - 2] . '_cropped_' . time();
            $crop_path = $this->lfm->setName(implode('.', $fileParts))->path('absolute');
        }

        event(new ImageIsCropping($image_path));

        $crop_info = request()->only('dataWidth', 'dataHeight', 'dataX', 'dataY');

        // crop image
        Image::make($image_path)
            ->crop(...array_values($crop_info))
            ->save($crop_path);

        // make new thumbnail
        $this->lfm->generateThumbnail($image_name);

        event(new ImageWasCropped($image_path));
    }

    public function getNewCropimage()
    {
        $this->getCropimage(false);
    }
}
