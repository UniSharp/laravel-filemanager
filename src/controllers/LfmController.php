<?php namespace Tsawler\Laravelfilemanager\controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Tsawler\Laravelfilemanager\requests\UploadRequest;

/**
 * Class LfmController
 * @package Tsawler\Laravelfilemanager\controllers
 */
class LfmController extends Controller {

    /**
     * Show the filemanager
     *
     * @return mixed
     */
    public function show()
    {
        if (Input::has('base'))
        {
            $working_dir = Input::get('base');
            $base = "/vendor/laravel-filemanager/files/" . Input::get('base') . "/";
        } else
        {
            $working_dir = "/";
            $base = "/vendor/laravel-filemanager/files/";
        }

        return View::make('laravel-filemanager::index')
            ->with('base', $base)
            ->with('working_dir', $working_dir);
    }


    /**
     * Upload an image and create thumbnail
     *
     * @param UploadRequest $request
     * @return string
     */
    public function upload(UploadRequest $request)
    {
        $file = Input::file('file_to_upload');
        $working_dir = Input::get('working_dir');
        $destinationPath = base_path() . "/" . Config::get('lfm.images_dir');

        if (strlen($working_dir) > 1)
        {
            $destinationPath .= $working_dir . "/";
        }

        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        $new_filename = Str::slug(str_replace($extension, '', $filename)) . "." . $extension;

        Input::file('file_to_upload')->move($destinationPath, $new_filename);

        if (!File::exists($destinationPath . "thumbs"))
        {
            File::makeDirectory($destinationPath . "thumbs");
        }

        $thumb_img = Image::make($destinationPath . $new_filename);
        $thumb_img->fit(200, 200)
            ->save($destinationPath . "thumbs/" . $new_filename);
        unset($thumb_img);

        if ($working_dir != "/")
            return Redirect::to('/laravel-filemanager?'.Config::get('lfm.params') . '&base=' . $working_dir);
        else
            return Redirect::to('/laravel-filemanager?'.Config::get('lfm.params'));
    }


    /**
     * Get data as json to populate treeview
     *
     * @return mixed
     */
    public function getData()
    {
        $directories = File::directories(base_path(Config::get('lfm.images_dir')));
        $final_array = [];
        foreach ($directories as $directory)
        {
            if (basename($directory) != "thumbs")
            {
                $final_array[] = basename($directory);
            }
        }

        return View::make("laravel-filemanager::tree")
            ->with('dirs', $final_array);
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
            if (File::exists(base_path() . "/" . Config::get('lfm.images_dir') . $base . "/" . $to_delete))
            {
                File::delete(base_path() . "/" . Config::get('lfm.images_dir') . $base . "/" . $to_delete);
                File::delete(base_path() . "/" . Config::get('lfm.images_dir') . $base . "/" . "thumbs/" . $to_delete);
            }
        } else
        {
            if (File::exists(base_path() . "/" . Config::get('lfm.images_dir') . $to_delete))
            {
                File::delete(base_path() . "/" . Config::get('lfm.images_dir') . $to_delete);
                File::delete(base_path() . "/" . Config::get('lfm.images_dir') . "thumbs/" . $to_delete);
            }
        }

        if (Input::get('base') != "/")
            return Redirect::to('/laravel-filemanager?'.Config::get('lfm.params').'&base=' . Input::get('base'));
        else
            return Redirect::to('/laravel-filemanager?'.Config::get('lfm.params'));
    }


    /**
     * Get the images to load for a selected folder
     *
     * @return mixed
     */
    public function getImages()
    {

        if (Input::has('base'))
        {
            $files = File::files(base_path(Config::get('lfm.images_dir') . Input::get('base')));
            $all_directories = File::directories(base_path(Config::get('lfm.images_dir') . Input::get('base')));
        } else
        {
            $files = File::files(base_path(Config::get('lfm.images_dir')));
            $all_directories = File::directories(base_path(Config::get('lfm.images_dir')));
        }

        $directories = [];

        foreach ($all_directories as $directory)
        {
            if (basename($directory) != "thumbs")
            {
                $directories[] = basename($directory);
            }
        }

        return View::make('laravel-filemanager::images')
            ->with('files', $files)
            ->with('directories', $directories)
            ->with('base', Input::get('base'));
    }


    /**
     * Add a new folder
     *
     * @return mixed
     */
    public function getAddfolder()
    {
        $folder_name = Input::get('name');
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


    /**
     * Show crop page
     *
     * @return mixed
     */
    public function getCrop()
    {
        $dir = Input::get('dir');
        $image = Input::get('img');

        return View::make('laravel-filemanager::crop')
            ->with('img', Config::get('lfm.images_url') . $dir . "/" . $image)
            ->with('dir', $dir)
            ->with('image', $image);
    }


    /**
     * Crop the image (called via ajax)
     */
    public function postCrop()
    {
        Log::info('cropping!');
        $dir = Input::get('dir');
        $img = Input::get('img');
        $dataX = Input::get('dataX');
        $dataY = Input::get('dataY');
        $dataHeight = Input::get('dataHeight');
        $dataWidth = Input::get('dataWidth');

        // crop image
        $image = Image::make(public_path() . $img);
        $image->crop($dataWidth, $dataHeight, $dataX, $dataY)
            ->save(public_path() . $img);

        // make new thumbnail
        $thumb_img = Image::make(public_path() . $img);
        $thumb_img->fit(200, 200)
            ->save(base_path() . "/" . Config::get('lfm.images_dir') . $dir . "/thumbs/" . basename($img));
    }

}
