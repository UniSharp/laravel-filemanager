<?php namespace Tsawler\Laravelfilemanager\controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;

/**
 * Class ScaleController
 * @package Tsawler\Laravelfilemanager\controllers
 */
class ScaleController extends Controller {

    /**
     * Dipsplay image for resizing
     *
     * @return mixed
     */
    public function getScale()
    {
        $ratio = 1.0;
        $image = Input::get('img');
        $dir = Input::get('dir');

        $original_width = Image::make(base_path() . "/" . Config::get('lfm.images_dir') . $dir . "/" . $image)->width();
        $original_height = Image::make(base_path() . "/" . Config::get('lfm.images_dir') . $dir . "/" . $image)->height();

        $scaled = false;

        if ($original_width > 600)
        {
            $ratio = 600 / $original_width;
            $width = $original_width * $ratio;
            $height = $original_height * $ratio;
            $scaled = true;
        } else
        {
            $height = $original_height;
            $width = $original_width;
        }

        if ($height > 400)
        {
            $ratio = 400 / $original_height;
            $width = $original_width * $ratio;
            $height = $original_height * $ratio;
            $scaled = true;
        }

        return View::make('laravel-filemanager::scale')
            ->with('img', Config::get('lfm.images_url') . $dir . "/" . $image)
            ->with('dir', $dir)
            ->with('image', $image)
            ->with('height', number_format($height, 0))
            ->with('width', $width)
            ->with('original_height', $original_height)
            ->with('original_width', $original_width)
            ->with('scaled', $scaled)
            ->with('ratio', $ratio);
    }
}
