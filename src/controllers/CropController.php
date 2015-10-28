<?php namespace Unisharp\Laravelfilemanager\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;

/**
 * Class CropController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class CropController extends LfmController {

    /**
     * Show crop page
     *
     * @return mixed
     */
    public function getCrop()
    {
        $dir = Input::get('dir');
        $image = Input::get('img');

        return View::make('laravel-filemanager::crop')
            ->with('img', Config::get('lfm.images_url') . $dir . "/" . $image)
            ->with('dir', $dir)
            ->with('image', $image);
    }


    /**
     * Crop the image (called via ajax)
     */
    public function getCropimage()
    {
        $dir = Input::get('dir');
        $img = Input::get('img');
        $dataX = Input::get('dataX');
        $dataY = Input::get('dataY');
        $dataHeight = Input::get('dataHeight');
        $dataWidth = Input::get('dataWidth');

        // crop image
        $image = Image::make(public_path() . $img);
        $image->crop($dataWidth, $dataHeight, $dataX, $dataY)
            ->save(public_path() . $img);

        // make new thumbnail
        $thumb_img = Image::make(public_path() . $img);
        $thumb_img->fit(200, 200)
            ->save(base_path() . "/" . Config::get('lfm.images_dir') . $dir . "/thumbs/" . basename($img));
    }

}
