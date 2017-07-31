<?php

namespace Unisharp\Laravelfilemanager\controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Unisharp\Laravelfilemanager\Events\ImageIsCropping;
use Unisharp\Laravelfilemanager\Events\ImageWasCropped;

/**
 * Class CropController.
 */
class CropController extends LfmController
{
    /**
     * Show crop page.
     *
     * @return mixed
     */
    public function getCrop(Request $request)
    {
        $working_dir = $request->input('working_dir');
        $img = parent::objectPresenter(parent::getCurrentPath($request->input('img')));

        return view('laravel-filemanager::crop')
            ->with(compact('working_dir', 'img'));
    }

    /**
     * Crop the image (called via ajax).
     */
    public function getCropimage($request, $overWrite = true)
    {
        $dataX = $request->input('dataX');
        $dataY = $request->input('dataY');
        $dataHeight = $request->input('dataHeight');
        $dataWidth = $request->input('dataWidth');
        $image_path = parent::getCurrentPath($request->input('img'));
        $crop_path = $image_path;

        if (! $overWrite) {
            $fileParts = explode('.', $request->input('img'));
            $fileParts[count($fileParts) - 2] = $fileParts[count($fileParts) - 2] . '_cropped_' . time();
            $crop_path = parent::getCurrentPath(implode('.', $fileParts));
        }

        event(new ImageIsCropping($image_path));
        // crop image
        Image::make($image_path)
            ->crop($dataWidth, $dataHeight, $dataX, $dataY)
            ->save($crop_path);

        // make new thumbnail
        Image::make($crop_path)
            ->fit(config('lfm.thumb_img_width', 200), config('lfm.thumb_img_height', 200))
            ->save(parent::getThumbPath(parent::getName($crop_path)));
        event(new ImageWasCropped($image_path));
    }

    public function getNewCropimage(Request $request)
    {
        $this->getCropimage($request, false);
    }
}
