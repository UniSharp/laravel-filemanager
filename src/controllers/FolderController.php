<?php

namespace Unisharp\Laravelfilemanager\controllers;

class FolderController extends LfmController
{
    /**
     * Get list of folders as json to populate treeview
     *
     * @return mixed
     */
    public function getFolders()
    {
        $folder_types = ['user', 'share'];
        foreach ($folder_types as $key => $type) {
            if (!parent::allowFolderType($type)) {
                unset($folder_types[$key]);
            }
        }

        return view('laravel-filemanager::tree')
            ->with([
                'root_folders' => array_map(function ($type) use ($folder_types) {
                    $root_folder_path = parent::getRootFolderPath($type);

                    return (object)[
                        'name' => trans('laravel-filemanager::lfm.title-' . $type),
                        'path' => parent::getInternalPath($root_folder_path),
                        'children' => parent::sortByColumn(parent::getDirectories($root_folder_path), 'name'),
                        'has_next' => !($type == end($folder_types))
                    ];
                }, $folder_types)
            ]);
    }


    /**
     * Add a new folder
     *
     * @return mixed
     */
    public function getAddfolder()
    {
        $folder_name = parent::translateFromUtf8(trim(request('name')));

        $path = parent::getCurrentPath($folder_name);

        if (empty($folder_name)) {
            return parent::error('folder-name');
        } elseif (parent::exists($path)) {
            return parent::error('folder-exist');
        } elseif (config('lfm.alphanumeric_directory') && preg_match('/[^\w-]/i', $folder_name)) {
            return parent::error('folder-alnum');
        } else {
            parent::createFolderByPath($path);
            return parent::$success_response;
        }
    }
}
