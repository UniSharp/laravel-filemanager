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
                'display' => $this->getDisplayType(),
            ]),
            'working_dir' => $this->lfm->path('working_dir'),
        ];
    }

    private function getDisplayType()
    {
        $type_key = $this->helper->currentLfmType();
        $startup_view = config('lfm.' . $type_key . 's_startup_view');

        $view_type = 'grid';
        $target_display_type = request('show_list') ?: $startup_view;

        if (in_array($target_display_type, ['list', 'grid'])) {
            $view_type = $target_display_type;
        }

        return $view_type;
    }
}
