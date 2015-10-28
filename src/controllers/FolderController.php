<?php namespace Unisharp\Laravelfilemanager\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

/**
 * Class FolderController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class FolderController extends LfmController {

    /**
     * Get list of folders as json to populate treeview
     *
     * @return mixed
     */
    public function getFolders()
    {
        $all_directories = File::directories(base_path($this->file_location . Input::get('base')));
        $directories = [];

        foreach ($all_directories as $directory) {
            if (basename($directory) != "thumbs") {
                $directories[] = basename($directory);
            }
        }

        $all_shares = File::directories(base_path($this->file_location . Config::get('lfm.shared_folder_name')));
        $shared_folders = [];

        foreach ($all_shares as $directory) {
            if (basename($directory) != "thumbs") {
                $shared_folders[] = basename($directory);
            }
        }

        return View::make("laravel-filemanager::tree")
            ->with('dirs', $directories)
            ->with('shares', $shared_folders);
    }


    /**
     * Add a new folder
     *
     * @return mixed
     */
    public function getAddfolder()
    {
        $folder_name = Input::get('name');

        $path = base_path($this->file_location . Input::get('base')) . "/" . $folder_name;

        if (!File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
            return "OK";
        } else if (empty($folder_name)) {
            return 'Folder name cannot be empty!';
        } else {
            return "A folder with this name already exists!";
        }
    }

}
