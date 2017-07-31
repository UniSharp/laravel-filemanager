<?php

namespace Unisharp\Laravelfilemanager\controllers;
use Illuminate\Http\Request;

/**
 * Class ItemsController.
 */
class ItemsController extends LfmController
{
    /**
     * Get the images to load for a selected folder.
     *
     * @return mixed
     */
    public function getItems(Request $request)
    {
        $path = parent::getCurrentPath();
        $sort_type = $request->input('sort_type');

        $files = parent::sortFilesAndDirectories(parent::getFilesWithInfo($path), $sort_type);
        $directories = parent::sortFilesAndDirectories(parent::getDirectories($path), $sort_type);

        return [
            'html' => (string) view($this->getView($request))->with([
                'files'       => $files,
                'directories' => $directories,
                'items'       => array_merge($directories, $files),
            ]),
            'working_dir' => parent::getInternalPath($path),
        ];
    }

    private function getView($request)
    {
        $view_type = 'grid';
        $show_list = $request->input('show_list');

        if ($show_list === '1') {
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
