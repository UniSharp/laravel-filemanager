<?php namespace Unisharp\Laravelfilemanager\controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;

/**
 * Class FolderController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class FolderController extends LfmController
{
    /**
     * Get list of folders as json to populate treeview
     *
     * @return mixed
     */
    public function getFolders()
    {
        $user_path     = parent::getPath('user');
        $lfm_user_path = parent::getFileName($user_path);
        $user_folders  = parent::getDirectories($user_path);

        $share_path     = parent::getPath('share');
        $lfm_share_path = parent::getFileName($share_path);
        $shared_folders = parent::getDirectories($share_path);

        return view('laravel-filemanager::tree')
            ->with('user_dir', $lfm_user_path['long'])
            ->with('dirs', $user_folders)
            ->with('share_dir', $lfm_share_path['long'])
            ->with('shares', $shared_folders);
    }


    /**
     * Add a new folder
     *
     * @return mixed
     */
    public function getAddfolder()
    {
        $folder_name = trim(Input::get('name'));

        $path = parent::getPath('directory') . $folder_name;

        if (empty($folder_name)) {
            return $this->error('folder-name');
        } elseif (File::exists($path)) {
            return $this->error('folder-exist');
        } elseif (Config::get('lfm.alphanumeric_directory') && preg_match('/[^\w-]/i', $folder_name)) {
            return $this->error('folder-alnum');
        } else {
            $this->createFolderByPath($path);
            return 'OK';
        }
    }
}
