<?php namespace Unisharp\Laravelfilemanager\controllers;

use Illuminate\Support\Facades\File;

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
        $path = $this->getCurrentPath();

        return [
            'html' => (string)view($this->getView())->with([
                'files'       => $this->getFilesWithInfo($path),
                'directories' => $this->getDirectories($path)
            ]),
            'working_dir' => $this->getInternalPath($path)
        ];
    }


    private function getView()
    {
        if (request('show_list') == 1) {
            $view_type = 'list';
        } else {
            $view_type = 'grid';
        }

        return 'laravel-filemanager::' . $view_type . '-view';
    }
}
