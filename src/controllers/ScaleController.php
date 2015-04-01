<?php namespace Tsawler\Laravelfilemanager\controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

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
        return View::make('laravel-filemanager::scale');
    }
}