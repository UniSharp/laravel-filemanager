<?php namespace Tsawler\Laravelfilemanager\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

/**
 * Class CropController
 * @package Tsawler\Laravelfilemanager\controllers
 */
class DeleteController extends Controller {

    /**
     * @var
     */
    protected $file_location;


    /**
     * constructor
     */
    function __construct()
    {
        if (Session::get('lfm_type') == "Images")
            $this->file_location = Config::get('lfm.images_dir');
        else
            $this->file_location = Config::get('lfm.files_dir');
    }


    /**
     * Delete image and associated thumbnail
     *
     * @return mixed
     */
    public function getDelete()
    {
        $to_delete = Input::get('items');
        $base = Input::get("base");

        if ($base != "/")
        {
            if (File::isDirectory(base_path() . "/" . $this->file_location . $to_delete))
            {
                File::delete(base_path() . "/" . $this->file_location . $base . "/" . $to_delete);

                return "OK";
            } else
            {
                if (File::exists(base_path() . "/" . $this->file_location . $base . "/" . $to_delete))
                {
                    File::delete(base_path() . "/" . $this->file_location . $base . "/" . $to_delete);

                    if (Session::get('lfm_type') == "Images'")
                        File::delete(base_path() . "/" . $this->file_location . $base . "/" . "thumbs/" . $to_delete);

                    return "OK";
                } else {
                    return base_path() . "/" . $this->file_location . $base . "/" . $to_delete
                        . " not found!";
                }
            }
        } else
        {
            $file_name = base_path() . "/" . $this->file_location . $to_delete;
            if (File::isDirectory($file_name))
            {
                // make sure the directory is empty
                if (sizeof(File::files($file_name)) == 0)
                {
                    File::deleteDirectory($file_name);

                    return "OK";
                } else
                {
                    return "You cannot delete this folder because it is not empty!";
                }
            } else
            {
                if (File::exists($file_name))
                {
                    File::delete($file_name);
                    if (Session::get('lfm_type') == "Images'")
                        File::delete(base_path() . "/" . $this->file_location . "thumbs/" . $to_delete);

                    return "OK";
                }
            }
        }
    }
    
}
