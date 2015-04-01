<?php namespace Tsawler\Laravelfilemanager\controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;

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

        /*
         * TODO: Look up image height/width
         */

        return View::make('laravel-filemanager::scale')
            ->with('img', Config::get('lfm.images_url') . $dir . "/" . $image)
            ->with('dir', $dir)
            ->with('image', $image)
            ->with('height', "100")
            ->with('width', "200");
    }
}