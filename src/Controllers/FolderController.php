<?php

namespace UniSharp\LaravelFilemanager\Controllers;

use Illuminate\Support\Str;
use UniSharp\LaravelFilemanager\Events\FolderIsCreating;
use UniSharp\LaravelFilemanager\Events\FolderWasCreated;
use UniSharp\LaravelFilemanager\LfmPath;

class FolderController extends LfmController
{
    /**
     * Get list of folders as json to populate treeview.
     *
     * @return mixed
     */
    public function getFolders()
    {
        $folder_types = array_filter(['user', 'share'], function ($type) {
            return $this->helper->allowFolderType($type);
        });

        return view('laravel-filemanager::tree')
            ->with([
                'root_folders' => array_map(function ($type) use ($folder_types) {
                    $path = app(LfmPath::class)->dir($this->helper->getRootFolder($type));

                    return (object) [
                        'name' => trans('laravel-filemanager::lfm.title-' . $type),
                        'url' => $path->workingDirectory(),
                        'children' => $path->folders(),
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
        $folder_name = $this->helper->input('name');

        $new_path = app(LfmPath::class)->setName($folder_name)->path('absolute');

        event(new FolderIsCreating($new_path));

        try {
            if ($folder_name === null || $folder_name == '') {
                return $this->helper->error('folder-name');
            }
            if ($this->lfm->setName($folder_name)->exists()) {
                return $this->helper->error('folder-exist');
            }

            if (config('lfm.alphanumeric_directory')) {
                if (config('lfm.convert_to_alphanumeric')) {
                    $folder_name = Str::slug($folder_name);
                } elseif (preg_match('/[^\w\-_]/i', $folder_name)) {
                    return $this->helper->error('folder-alnum');
                }
            }

            $this->lfm->setName($folder_name)->createFolder();
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        event(new FolderWasCreated($new_path));

        return parent::$success_response;
    }
}
