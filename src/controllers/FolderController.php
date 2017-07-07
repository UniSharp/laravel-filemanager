<?php

namespace Unisharp\Laravelfilemanager\controllers;

class FolderController extends LfmController
{
    /**
     * Get list of folders as json to populate treeview.
     *
     * @return mixed
     */
    public function getFolders()
    {
        $folder_types = ['user', 'share'];
        foreach ($folder_types as $key => $type) {
            if (! parent::allowFolderType($type)) {
                unset($folder_types[$key]);
            }
        }

        return view('laravel-filemanager::tree')
            ->with([
                'root_folders' => array_map(function ($type) use ($folder_types) {
                    $path = $this->lfm->dir(parent::rootFolder($type));

                    return (object) [
                        'name' => trans('laravel-filemanager::lfm.title-' . $type),
                        'path' => $path->path('working_dir'),
                        'children' => parent::sortByColumn($path->folders(), 'name'),
                        'has_next' => ! ($type == end($folder_types)),
                    ];
                }, $folder_types),
            ]);
    }

    /**
     * Add a new folder.
     *
     * @return mixed
     */
    public function getAddfolder()
    {
        $folder_name = parent::translateFromUtf8(trim(request('name')));

        if (empty($folder_name)) {
            return parent::error('folder-name');
        } elseif ($this->lfm->setName($folder_name)->exists()) {
            return parent::error('folder-exist');
        } elseif (config('lfm.alphanumeric_directory') && preg_match('/[^\w-]/i', $folder_name)) {
            return parent::error('folder-alnum');
        } else {
            $this->lfm->setName($folder_name)->createFolder();

            return parent::$success_response;
        }
    }
}
