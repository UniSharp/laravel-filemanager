<?php namespace Tsawler\Laravelfilemanager\controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
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
        Input::file('file_to_upload')->move($destinationPath, $filename);

        if (!File::exists($destinationPath . "thumbs"))
        {
            File::makeDirectory($destinationPath . "thumbs");
        }

        $thumb_img = Image::make($destinationPath . $filename);
        $thumb_img->fit(200, 200)
            ->save($destinationPath . "thumbs/" . $filename);
        unset($thumb_img);

        if ($working_dir != "/")
            return Redirect::to('/laravel-filemanager?base=' . $working_dir);
        else
            return Redirect::to('/laravel-filemanager');
    }


    /**
     * Get data as json to populate treeview
     *
     * @return mixed
     */
    public function getData()
    {
        $contents = File::files(base_path(Config::get('lfm.images_dir')));
        $directories = File::directories(base_path(Config::get('lfm.images_dir')));

        $dir_array = [];


        // go through all directories
        foreach ($directories as $dir)
        {
            if (basename($dir) != "thumbs")
            {
                $dir_contents = File::files($dir);
                $children = [];

                foreach ($dir_contents as $c)
                {
                    $children[] = ['label' => basename($c), 'id' => Str::slug(basename($dir)) . "-" . Str::slug(basename($c))];
                }

                if (sizeof($children) == 0)
                {
                    $children[] = ['label' => '(empty)', 'id' => Str::slug(basename($dir) . '-empty')];
                }

                $dir_array[] = ['label' => basename($dir), 'id' => Str::slug(basename($dir)), 'children' => $children];
            }

        }
        $final_array = [];

        $final_array[] = ['label' => 'Files', 'id' => 'rootofdir', 'children' => $dir_array];

        foreach ($contents as $c)
        {
            $dir_array[] = ['label' => basename($c), 'id' => Str::slug(basename($c))];
        }

        return response()->json($final_array);
    }


    /**
     * Delete image and associated thumbnail
     *
     * @return mixed
     */
    public function getDelete()
    {
        $json = Input::get('items');
        $to_delete = json_decode($json);
        $base = Input::get("base");

        foreach ($to_delete as $item)
        {
            if ($base != "/")
            {
                if (File::exists(base_path() . "/" . Config::get('lfm.images_dir') . $base . "/" . $item))
                {
                    File::delete(base_path() . "/" . Config::get('lfm.images_dir') . $base . "/" . $item);
                    File::delete(base_path() . "/" . Config::get('lfm.images_dir') . $base . "/" . "thumbs/" . $item);
                }
            } else
            {
                if (File::exists(base_path() . "/" . Config::get('lfm.images_dir') . $item))
                {
                    File::delete(base_path() . "/" . Config::get('lfm.images_dir') . $item);
                    File::delete(base_path() . "/" . Config::get('lfm.images_dir') . "thumbs/" . $item);
                }
            }
        }
        if (Input::get('base') != "/")
            return Redirect::to('/laravel-filemanager?base=' . Input::get('base'));
        else
            return Redirect::to('/laravel-filemanager');
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
            $directories = File::directories(base_path(Config::get('lfm.images_dir') . Input::get('base')));
        } else
        {
            $files = File::files(base_path(Config::get('lfm.images_dir')));
            $directories = File::directories(base_path(Config::get('lfm.images_dir')));
        }

        return View::make('laravel-filemanager::images')
            ->with('files', $files)
            ->with('directories', $directories)
            ->with('base', Input::get('base'));
    }

}
