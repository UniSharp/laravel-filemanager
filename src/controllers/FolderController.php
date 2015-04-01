<?php namespace Tsawler\Laravelfilemanager\controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

/**
 * Class FolderController
 * @package Tsawler\Laravelfilemanager\controllers
 */
class FolderController extends Controller {

    /**
     * Add a new folder
     *
     * @return mixed
     */
    public function getAddfolder()
    {
        $folder_name = Str::slug(Input::get('name'));
        $path = base_path(Config::get('lfm.images_dir'));

        if( ! File::exists($path . $folder_name)) {
            File::makeDirectory($path . $folder_name, $mode = 0777, true, true);
        }

        return Redirect::to('/laravel-filemanager?'.Config::get('lfm.params'));
    }


    /**
     * Delete a folder and all of it's contents
     *
     * @return mixed
     */
    public function getDeletefolder()
    {
        $folder_name = Input::get('name');
        $path = base_path(Config::get('lfm.images_dir'));
        File::deleteDirectory($path . $folder_name, $preserve = false);

        return Redirect::to('/laravel-filemanager?'.Config::get('lfm.params'));
    }
}