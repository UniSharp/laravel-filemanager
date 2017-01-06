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
        $type = $this->currentLfmType(true);
        $view = $this->getView();
        $path = parent::getCurrentPath();

        $files       = File::files($path);
        $file_info   = $this->getFileInfos($files);
        $directories = parent::getDirectories($path);
        $thumb_url   = parent::getThumbUrl();

        return view($view)
            ->with(compact('type', 'file_info', 'directories', 'thumb_url'));
    }


    private function getFileInfos($files)
    {
        $file_info = [];

        foreach ($files as $key => $file) {
            $file_name = parent::getFileName($file)['short'];
            $file_created = filemtime($file);
            $file_size = number_format((File::size($file) / 1024), 2, ".", "");

            if ($file_size > 1024) {
                $file_size = number_format(($file_size / 1024), 2, ".", "") . " Mb";
            } else {
                $file_size = $file_size . " Kb";
            }

            if ($this->isProcessingImages()) {
                $file_type = File::mimeType($file);
                $icon = '';
            } else {
                $extension = strtolower(File::extension($file_name));

                $icon_array = config('lfm.file_icon_array');
                $type_array = config('lfm.file_type_array');

                if (array_key_exists($extension, $icon_array)) {
                    $icon = $icon_array[$extension];
                    $file_type = $type_array[$extension];
                } else {
                    $icon = "fa-file";
                    $file_type = "File";
                }
            }

            $file_info[$key] = [
                'name'      => $file_name,
                'size'      => $file_size,
                'created'   => $file_created,
                'type'      => $file_type,
                'icon'      => $icon,
            ];
        }

        return $file_info;
    }


    private function getView()
    {
        if (request('show_list') == 1) {
            return 'laravel-filemanager::list-view';
        } else {
            return 'laravel-filemanager::grid-view';
        }
    }
}
