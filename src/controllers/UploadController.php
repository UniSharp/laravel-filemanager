<?php namespace Unisharp\Laravelfilemanager\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

/**
 * Class UploadController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class UploadController extends LfmController {

    /**
     * Upload an image/file and (for images) create thumbnail
     *
     * @param UploadRequest $request
     * @return string
     */
    public function upload()
    {
        // sanity check
        if ( ! Input::hasFile('upload')) {
            // there ws no uploded file
            return 'You must choose a file!';
        }

        $file = Input::file('upload');

        $new_filename = $this->getNewName($file);

        $destinationPath = parent::getPath();

        if (File::exists($destinationPath . $new_filename)) {
            return 'A file with this name already exists!';
        }

        $file->move($destinationPath, $new_filename);

        if (Session::get('lfm_type') == 'Images') {
            $this->makeThumb($destinationPath, $new_filename);
        }

        if (Input::get('uploadMode') === 'express') {
            # code...
        }

        return 'OK';
    }

    private function getNewName($file)
    {
        $new_filename = $file->getClientOriginalName();

        if (Config::get('lfm.rename_file') === true) {
            $new_filename = uniqid() . '.' . $file->getClientOriginalExtension();
        }

        return $new_filename;
    }

    private function makeThumb($destinationPath, $new_filename)
    {
        $thumb_folder_name = Config::get('lfm.thumb_folder_name');

        if (!File::exists($destinationPath . $thumb_folder_name)) {
            File::makeDirectory($destinationPath . $thumb_folder_name);
        }

        $thumb_img = Image::make($destinationPath . $new_filename);
        $thumb_img->fit(200, 200)
            ->save($destinationPath . $thumb_folder_name . '/' . $new_filename);
        unset($thumb_img);
    }

}
