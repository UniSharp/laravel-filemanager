<?php

namespace UniSharp\LaravelFilemanager\Controllers;

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
            'items' => array_map(function ($item) {
                return $item->fill()->attributes;
            }, array_merge($this->lfm->folders(), $this->lfm->files())),
            'display' => $this->helper->getDisplayMode(),
            'working_dir' => $this->lfm->path('working_dir'),
        ];
    }
}
