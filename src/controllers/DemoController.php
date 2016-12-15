<?php namespace Unisharp\Laravelfilemanager\controllers;

/**
 * Class DemoController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class DemoController extends LfmController
{

    /**
     * @return mixed
     */
    public function index()
    {
        return view('laravel-filemanager::demo');
    }
}
