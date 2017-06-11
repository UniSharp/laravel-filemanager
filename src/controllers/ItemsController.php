<?php namespace Unisharp\Laravelfilemanager\controllers;

use Unisharp\FileApi\FileApi;

/**
 * Class ItemsController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class ItemsController extends LfmController
{
    /**
     * Get the images to load for a selected folder
     *
     * @return mixed
     */
    public function getItems()
    {
        $path = parent::getCurrentPath();
        $sort_type = request('sort_type');

        if ($sort_type == 'time') {
            $key_to_sort = 'updated';
        } elseif ($sort_type == 'alphabetic') {
            $key_to_sort = 'name';
        } else {
            $key_to_sort = 'updated';
        }

        // \Log::info(\File::directories($path));

        $files = parent::sortByColumn(parent::getFilesWithInfo($path), $key_to_sort);
        $directories = parent::sortByColumn(parent::getDirectories($path), $key_to_sort);

        return [
            'html' => (string)view($this->getView())->with([
                'files'       => $files,
                'directories' => $directories,
                'items'       => array_merge($directories, $files)
            ]),
            'working_dir' => parent::getInternalPath($path)
        ];
    }


    private function getView()
    {
        $view_type = 'grid';
        $show_list = request('show_list');

        if ($show_list === "1") {
            $view_type = 'list';
        } elseif (is_null($show_list)) {
            $type_key = parent::currentLfmType();
            $startup_view = config('lfm.' . $type_key . 's_startup_view');

            if (in_array($startup_view, ['list', 'grid'])) {
                $view_type = $startup_view;
            }
        }

        return 'laravel-filemanager::' . $view_type . '-view';
    }
}
