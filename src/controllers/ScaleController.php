<?php namespace Tsawler\Laravelfilemanager\controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use Intervention\Image\Facades\Image;

/**
 * Class ScaleController
 * @package Tsawler\Laravelfilemanager\controllers
 */
class ScaleController extends Controller {

    /**
     * @return mixed
     */
    public function getScale()
    {
        $image = Input::get('img');
        $dir = Input::get('dir');

        $width = Image::make(base_path() . "/" . Config::get('lfm.images_dir') . $dir . "/" .  $image)->width();
        $height = Image::make(base_path() . "/" . Config::get('lfm.images_dir') . $dir . "/" .  $image)->height();

        return View::make('laravel-filemanager::scale')
            ->with('img', Config::get('lfm.images_url') . $dir . "/" . $image)
            ->with('dir', $dir)
            ->with('image', $image)
            ->with('height', $height)
            ->with('width', $width);
    }
}