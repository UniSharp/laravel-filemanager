<?php namespace Tsawler\Laravelfilemanager\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;

/**
 * Class LfmController
 * @package Tsawler\Laravelfilemanager\controllers
 */
class LfmController extends Controller {

    /**
     * @var
     */
    protected $file_location;

    /**
     * Constructor
     */
    public function __construct()
    {
        if ((Session::has('lfm_type')) && (Session::get('lfm_type') == 'Files'))
        {
            $this->file_location = Config::get('lfm.files_dir');
        } else
        {
            $this->file_location = Config::get('lfm.images_dir');
        }
    }


    /**
     * Show the filemanager
     *
     * @return mixed
     */
    public function show()
    {
        if ((Input::has('type')) && (Input::get('type') == "Files"))
        {
            Session::put('lfm_type', 'Files');
            $this->file_location = Config::get('lfm.files_dir');
        } else
        {
            Session::put('lfm_type', 'Images');
            $this->file_location = Config::get('lfm.images_dir');
        }

        if (Input::has('base'))
        {
            $working_dir = Input::get('base');
            $base = $this->file_location . Input::get('base') . "/";
        } else
        {
            $working_dir = "/";
            $base = $this->file_location;
        }

        return View::make('laravel-filemanager::index')
            ->with('base', $base)
            ->with('working_dir', $working_dir);
    }



    public function getFiles()
    {
        return "List of files";
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
