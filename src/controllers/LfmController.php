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
            return Redirect::to('/laravel-filemanager?' . Config::get('lfm.params') . '&base=' . $working_dir);
        else
            return Redirect::to('/laravel-filemanager?' . Config::get('lfm.params'));
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
        Log::info('base is ' . $base);

        if ($base != "/")
        {
            if (File::isDirectory(base_path() . "/" . Config::get('lfm.images_dir') . $to_delete))
            {
                File::delete(base_path() . "/" . Config::get('lfm.images_dir') . $base . "/" . $to_delete);

                return "OK";
            } else
            {
                if (File::exists(base_path() . "/" . Config::get('lfm.images_dir') . $base . "/" . $to_delete))
                {
                    File::delete(base_path() . "/" . Config::get('lfm.images_dir') . $base . "/" . $to_delete);
                    File::delete(base_path() . "/" . Config::get('lfm.images_dir') . $base . "/" . "thumbs/" . $to_delete);

                    return "OK";
                }
            }
        } else
        {
            $file_name = base_path() . "/" . Config::get('lfm.images_dir') . $to_delete;
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
                    File::delete(base_path() . "/" . Config::get('lfm.images_dir') . "thumbs/" . $to_delete);

                    return "OK";
                }
            }
        }
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

        $file_info = [];

        foreach ($files as $file)
        {
            $file_name = $file;
            $file_size = number_format((Image::make($file)->filesize() / 1024), 2, ".", "");
            if ($file_size > 1000)
            {
                $file_size = number_format((Image::make($file)->filesize() / 1024), 2, ".", "") . " Mb";
            } else
            {
                $file_size = $file_size . " Kb";
            }
            $file_created = filemtime($file);
            $file_type = Image::make($file)->mime();
            $file_info[] = [
                'name'    => $file_name,
                'size'    => $file_size,
                'created' => $file_created,
                'type'    => $file_type
            ];
        }

        if (Input::get('show_list') == 1)
        {
            return View::make('laravel-filemanager::images-list')
                ->with('directories', $directories)
                ->with('base', Input::get('base'))
                ->with('file_info', $file_info);
        } else
        {
            return View::make('laravel-filemanager::images')
                ->with('files', $files)
                ->with('directories', $directories)
                ->with('base', Input::get('base'));
        }
    }

}
