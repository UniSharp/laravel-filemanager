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
        return [
            'html' => (string) view('laravel-filemanager::items')->with([
                'items' => array_merge($this->lfm->folders(), $this->lfm->files()),
                'display' => $this->helper->getDisplayMode(),
            ]),
            'working_dir' => $this->lfm->path('working_dir'),
        ];
    }
}
