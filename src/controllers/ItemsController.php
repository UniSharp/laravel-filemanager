<?php

namespace UniSharp\LaravelFilemanager\controllers;

class ItemsController extends LfmController
{
    /**
     * Get the images to load for a selected folder.
     *
     * @return mixed
     */
    public function getItems()
    {
        if (request('sort_type') == 'alphabetic') {
            $key_to_sort = 'name';
        } else {
            $key_to_sort = 'time';
        }

        return [
            'html' => (string) view('laravel-filemanager::items')->with([
                'items' => array_merge(
                    $this->lfm->folders($key_to_sort),
                    $this->lfm->files($key_to_sort)
                ),
                'display' => $this->helper->getDisplayMode(),
            ]),
            'working_dir' => $this->lfm->path('working_dir'),
        ];
    }
}
