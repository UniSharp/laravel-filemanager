<?php namespace Unisharp\Laravelfilemanager\controllers;

use Unisharp\Laravelfilemanager\controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Lang;

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
        $dir_path = parent::getPath();
        $directories = parent::getDirectories($dir_path);

        $share_path = parent::getPath('share');
        $shared_folders = parent::getDirectories($share_path);

        return View::make('laravel-filemanager::tree')
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

        $path = parent::getPath() . $folder_name;

        if (!File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
            return 'OK';
        } else if (empty($folder_name)) {
            return Lang::get('laravel-filemanager::lfm.error-folder-name');
        } else {
            return Lang::get('laravel-filemanager::lfm.error-folder-exist');
        }
    }

}
