<?php namespace Unisharp\Laravelfilemanager\controllers;

use Unisharp\Laravelfilemanager\controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

/**
 * Class CropController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class CropController extends LfmController
{
    /**
     * Show crop page
     *
     * @return mixed
     */
    public function getCrop()
    {
        $working_dir = request('working_dir');
        $img = parent::getFileUrl(request('img'));

        return view('laravel-filemanager::crop')
            ->with(compact('working_dir', 'img'));
    }


    /**
     * Crop the image (called via ajax)
     */
    public function getCropimage()
    {
        $image      = request('img');
        $dataX      = request('dataX');
        $dataY      = request('dataY');
        $dataHeight = request('dataHeight');
        $dataWidth  = request('dataWidth');
        $image_path = public_path() . $image;

        // crop image
        Image::make($image_path)
            ->crop($dataWidth, $dataHeight, $dataX, $dataY)
            ->save($image_path);

        return [
            'thumb' => parent::getThumbPath(parent::getName($image_path))
        ];

        File::delete(parent::getThumbPath(parent::getName($image_path)));

        // make new thumbnail
        /*Image::make($image_path)
            ->fit(200, 200)
            ->save(parent::getThumbPath(parent::getName($image)));*/
    }
}
