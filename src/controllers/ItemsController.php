<?php

namespace Unisharp\Laravelfilemanager\controllers;

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
    public function getItems()
    {
        $path = parent::getCurrentPath();
        $sort_type = request('sort_type');

        $files = parent::sortFilesAndDirectories(parent::getFilesWithInfo($path), $sort_type);
        $directories = parent::sortFilesAndDirectories(parent::getDirectories($path), $sort_type);

        return [
            'html' => (string) view($this->getView())->with([
                'files'       => $files,
                'directories' => $directories,
                'items'       => array_merge($directories, $files),
            ]),
            'working_dir' => parent::getInternalPath($path),
        ];
    }

    private function getView()
    {
        $view_type = request('show_list');

        if (null === $view_type) {
            return $this->composeViewName($this->getStartupViewFromConfig());
        }

        $view_mapping = [
            '0' => 'grid',
            '1' => 'list'
        ];

        return $this->composeViewName($view_mapping[$view_type]);
    }

    private function composeViewName($view_type = 'grid')
    {
        return "laravel-filemanager::$view_type-view";
    }

    private function getStartupViewFromConfig($default = 'grid')
    {
        $type_key = parent::currentLfmType();
        $startup_view = config('lfm.' . $type_key . 's_startup_view', $default);
        return $startup_view;
    }
}
