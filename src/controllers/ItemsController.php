<?php namespace Tsawler\Laravelfilemanager\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;

/**
 * Class ItemsController
 * @package Tsawler\Laravelfilemanager\controllers
 */
class ItemsController extends Controller {


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
     * Return json list of files
     *
     * @return mixed
     */
    public function getFiles()
    {
        if (Input::has('base'))
        {
            $files = File::files(base_path($this->file_location . Input::get('base')));
            $all_directories = File::directories(base_path($this->file_location . Input::get('base')));
        } else
        {
            $files = File::files(base_path($this->file_location));
            $all_directories = File::directories(base_path($this->file_location));
        }

        $directories = [];

        foreach ($all_directories as $directory)
        {
            $directories[] = basename($directory);
        }

        $file_info = [];
        $icon_array = Config::get('lfm.icon_array');
        $type_array = Config::get('lfm.type_array');

        foreach ($files as $file)
        {
            $file_name = $file;
            $file_size = 1;
            $extension = strtolower(File::extension($file_name));

            if (array_key_exists($extension, $icon_array))
            {
                $icon = $icon_array[$extension];
                $type = $type_array[$extension];
            } else
            {
                $icon = "fa-file";
                $type= "File";
            }

            $file_created = filemtime($file);
            $file_type = '';
            $file_info[] = [
                'name'      => $file_name,
                'size'      => $file_size,
                'created'   => $file_created,
                'type'      => $file_type,
                'extension' => $extension,
                'icon'      => $icon,
                'type'      => $type,
            ];
        }


        if (Input::get('show_list') == 1)
        {
            return View::make('laravel-filemanager::files-list')
                ->with('directories', $directories)
                ->with('base', Input::get('base'))
                ->with('file_info', $file_info)
                ->with('dir_location', $this->file_location);
        } else
        {
            return View::make('laravel-filemanager::files')
                ->with('files', $files)
                ->with('directories', $directories)
                ->with('base', Input::get('base'))
                ->with('file_info', $file_info)
                ->with('dir_location', $this->file_location);
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
            $files = File::files(base_path($this->file_location . Input::get('base')));
            $all_directories = File::directories(base_path($this->file_location . Input::get('base')));
        } else
        {
            $files = File::files(base_path($this->file_location));
            $all_directories = File::directories(base_path($this->file_location));
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

        if ((Session::has('lfm_type')) && (Session::get('lfm_type') == "Images"))
            $dir_location = Config::get('lfm.images_url');
        else
            $dir_location = Config::get('lfm.files_url');

        if (Input::get('show_list') == 1)
        {
            return View::make('laravel-filemanager::images-list')
                ->with('directories', $directories)
                ->with('base', Input::get('base'))
                ->with('file_info', $file_info)
                ->with('dir_location', $dir_location);
        } else
        {
            return View::make('laravel-filemanager::images')
                ->with('files', $files)
                ->with('directories', $directories)
                ->with('base', Input::get('base'))
                ->with('dir_location', $dir_location);
        }

    }

}
