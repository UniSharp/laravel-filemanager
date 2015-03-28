<?php namespace Tsawler\Laravelfilemanager\controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Tsawler\Laravelfilemanager\requests\UploadRequest;


/**
 * Class LfmController
 * @package Tsawler\Laravelfilemanager\controllers
 */
class LfmController extends Controller {

    public function show()
    {
        return View::make('laravel-filemanager::index');
    }

    public function upload(UploadRequest $request)
    {
        return "foobar";
    }

}