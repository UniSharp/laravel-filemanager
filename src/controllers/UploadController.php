<?php namespace Tsawler\Laravelfilemanager\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

/**
 * Class UploadController
 * @package Tsawler\Laravelfilemanager\controllers
 */
class UploadController extends Controller {

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
     * Upload an image/file and (for images) create thumbnail
     *
     * @param UploadRequest $request
     * @return string
     */
    public function upload()
    {
        // sanity check
        if ( ! Input::hasFile('file_to_upload'))
        {
            // there ws no uploded file
            return "You must choose a file!";
            exit;
        }


        if (Session::get('lfm_type') == "Images")
        {
            $file = Input::file('file_to_upload');
            $working_dir = Input::get('working_dir');
            $destinationPath = base_path() . "/" . $this->file_location;

            if (strlen($working_dir) > 1)
            {
                $destinationPath .= $working_dir . "/";
            }

            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $new_filename = Str::slug(str_replace($extension, '', $filename)) . "." . $extension;
            // new_filename is null, replace it with uuid
            $new_filename = uniqid() . "." . $extension;

            Input::file('file_to_upload')->move($destinationPath, $new_filename);

            if (!File::exists($destinationPath . "thumbs"))
            {
                File::makeDirectory($destinationPath . "thumbs");
            }

            $thumb_img = Image::make($destinationPath . $new_filename);
            $thumb_img->fit(200, 200)
                ->save($destinationPath . "thumbs/" . $new_filename);
            unset($thumb_img);

            return "OK";
        } else
        {
            $file = Input::file('file_to_upload');
            $working_dir = Input::get('working_dir');
            $destinationPath = base_path() . "/" . $this->file_location;

            if (strlen($working_dir) > 1)
            {
                $destinationPath .= $working_dir . "/";
            }

            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $new_filename = Str::slug(str_replace($extension, '', $filename)) . "." . $extension;

            if (File::exists($destinationPath . $new_filename))
            {
                return "A file with this name already exists!";
                exit;
            }

            Input::file('file_to_upload')->move($destinationPath, $new_filename);

            return "OK";
        }

    }

}
