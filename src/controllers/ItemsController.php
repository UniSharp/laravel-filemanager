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
        $sort_type = request('sort_type');

        if ($sort_type == 'time') {
            $key_to_sort = 'updated';
        } elseif ($sort_type == 'alphabetic') {
            $key_to_sort = 'name';
        } else {
            $key_to_sort = 'updated';
        }

        return [
            'html' => (string) view($this->getView())->with([
                'items' => array_merge(
                    parent::sortByColumn($this->lfm->folders(), $key_to_sort),
                    parent::sortByColumn($this->lfm->files(), $key_to_sort)
                ),
            ]),
            'working_dir' => $this->lfm->path('working_dir'),
        ];
    }

    private function getView()
    {
        $view_type = 'grid';
        $show_list = request('show_list');

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
