<?php namespace Unisharp\Laravelfilemanager\controllers;

use Illuminate\Support\Facades\File;

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
        $folder_types = [];
        $root_folders = [];

        if (parent::allowMultiUser()) {
            $folder_types['user'] = 'root';
        }

        if (true) {
            $folder_types['share'] = 'shares';
        }

        foreach ($folder_types as $folder_type => $lang_key) {
            $root_folder_path = parent::getRootFolderPath($folder_type);

            array_push($root_folders, (object)[
                'name' => trans('laravel-filemanager::lfm.title-' . $lang_key),
                'path' => parent::getInternalPath($root_folder_path),
                'children' => parent::getDirectories($root_folder_path),
                'has_next' => !($lang_key == end($folder_types))
            ]);
        }

        return view('laravel-filemanager::tree')
            ->with(compact('root_folders'));
    }


    /**
     * Add a new folder
     *
     * @return mixed
     */
    public function getAddfolder()
    {
        $folder_name = trim(request('name'));

        $path = parent::getCurrentPath($folder_name);

        if (empty($folder_name)) {
            return $this->error('folder-name');
        } elseif (File::exists($path)) {
            return $this->error('folder-exist');
        } elseif (config('lfm.alphanumeric_directory') && preg_match('/[^\w-]/i', $folder_name)) {
            return $this->error('folder-alnum');
        } else {
            $this->createFolderByPath($path);
            return 'OK';
        }
    }
}
