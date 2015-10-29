<?php namespace Unisharp\Laravelfilemanager\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;

/**
 * Class LfmController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class LfmController extends Controller {

    /**
     * @var
     */
    public $file_location;
    public $dir_location;
    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setFilePath();

        $this->setDirPath();
        
        $this->checkMyFolderExists();
        
        $this->checkSharedFolderExists();
    }


    private function setFilePath()
    {
        if ((Session::has('lfm_type')) && (Session::get('lfm_type') == 'Files')) {
            $this->file_location = Config::get('lfm.files_dir');
        } else {
            $this->file_location = Config::get('lfm.images_dir');
        }
    }


    private function setDirPath()
    {
        if ((Session::has('lfm_type')) && (Session::get('lfm_type') == "Images")) {
            $this->dir_location = Config::get('lfm.images_url');
        } else {
            $this->dir_location = Config::get('lfm.files_url');
        }
    }


    private function checkMyFolderExists()
    {
        if (\Config::get('lfm.allow_multi_user') === true) {
            $path = base_path($this->file_location . Input::get('base'));

            if (!File::exists($path)) {
                File::makeDirectory($path, $mode = 0777, true, true);
            }
        }
    }


    private function checkSharedFolderExists()
    {
        $path = base_path($this->file_location . Config::get('lfm.shared_folder_name'));

        if (!File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }
    }


    public function getDirectories($path)
    {
        $all_directories = File::directories(base_path($path));

        $arr_dir = [];

        foreach ($all_directories as $directory) {
            $path_parts = explode('/', $directory);
            $dir_name = end($path_parts);

            if ($dir_name !== 'thumbs') {
                $arr_dir[] = $dir_name;
            }
        }

        return $arr_dir;
    }


    /**
     * Show the filemanager
     *
     * @return mixed
     */
    public function show()
    {
        if ((Input::has('type')) && (Input::get('type') == "Files")) {
            Session::put('lfm_type', 'Files');
        } else {
            Session::put('lfm_type', 'Images');
        }

        if (Input::has('base')) {
            $working_dir = Input::get('base');
            $base = $this->file_location . Input::get('base') . "/";
        } else {
            $working_dir = "/";
            $base = $this->file_location;
        }

        return View::make('laravel-filemanager::index')
            ->with('base', $base)
            ->with('working_dir', $working_dir);
    }

}
