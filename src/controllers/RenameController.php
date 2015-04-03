<?php namespace Tsawler\Laravelfilemanager\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;

/**
 * Class RenameController
 * @package Tsawler\Laravelfilemanager\controllers
 */
class RenameController extends Controller {

    /**
     * @return string
     */
    function getRename(){

        $file_to_rename = Input::get('file');
        $dir = Input::get('dir');
        $new_name = Str::slug(Input::get('new_name'));

        if ($dir == "/")
        {
            if (File::exists(base_path() . "/". Config::get('lfm.images_dir') . $new_name))
            {
                return "File name already in use!";
            } else
            {
                if (File::isDirectory(base_path() . "/" . Config::get('lfm.images_dir') . $file_to_rename))
                {
                    File::move(base_path() . "/" . Config::get('lfm.images_dir') . $file_to_rename,
                        base_path() . "/" . Config::get('lfm.images_dir') . $new_name);

                    return "OK";
                } else
                {
                    File::move(base_path() . "/" . Config::get('lfm.images_dir') . $file_to_rename,
                        base_path() . "/" . Config::get('lfm.images_dir') . $new_name);
                    // rename thumbnail
                    File::move(base_path() . "/" . Config::get('lfm.images_dir') . "thumbs/" . $file_to_rename,
                        base_path() . "/" . Config::get('lfm.images_dir') . "thumbs/" . $new_name);

                    return "OK";
                }
            }
        } else
        {
            if (File::exists(base_path() . "/" . Config::get('lfm.images_dir') . $dir . "/" . $new_name))
            {
                return "File name already in use!";
            } else
            {
                //return base_path() . "/" . Config::get('lfm.images_dir') . $dir . "/" . $file_to_rename;

                if (File::isDirectory(base_path() . "/" . Config::get('lfm.images_dir') . $dir . "/" . $file_to_rename))
                {
                    File::move(base_path() . "/" . Config::get('lfm.images_dir') . $dir . "/" . $file_to_rename,
                        base_path() . "/" . Config::get('lfm.images_dir') . $dir . "/" . $new_name);
                } else
                {
                    File::move(base_path() . "/" . Config::get('lfm.images_dir') . $dir . "/" . $file_to_rename,
                        base_path() . "/" . Config::get('lfm.images_dir') . $dir . "/" . $new_name);
                    // rename thumbnail
                    File::move(base_path() . "/" . Config::get('lfm.images_dir') . $dir . "/thumbs/" . $file_to_rename,
                        base_path() . "/" . Config::get('lfm.images_dir') . $dir . "/thumbs/" . $new_name);

                    return "OK";
                }
            }
        }

    }
}
