<?php namespace Unisharp\Laravelfilemanager\controllers;

/**
 * Class DebugController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class DebugController extends LfmController
{

    /**
     * show the basic information for debug
     *
     * @return mixed
     */
    public function index()
    {
        if (env('APP_DEBUG') == false)
            abort(404);

        $string = file_get_contents('../composer.lock');
        $json = json_decode($string);
        foreach ($json->packages as $package) {
            if ($package->name == 'unisharp/laravel-filemanager') {
                $lfmVersion = $package->version;
                break;
            }
        }

        return view('laravel-filemanager::debug')->with('lfmVersion', $lfmVersion);
    }
}
