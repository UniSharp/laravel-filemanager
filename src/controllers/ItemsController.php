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
        $path = parent::getCurrentPath();

        return view($this->getView())->with([
            'files'       => $this->getFilesWithInfo($path),
            'directories' => parent::getDirectories($path)
        ]);
    }


    private function getFilesWithInfo($path)
    {
        $arr_files = [];

        foreach (File::files($path) as $key => $file) {
            $file_name = parent::getName($file);
            $file_url = parent::getFileUrl($file_name);
            $file_created = filemtime($file);
            $file_size = $this->humanFilesize(File::size($file));

            if ($this->isProcessingImages()) {
                $file_type = File::mimeType($file);
                $icon = 'fa-image';
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

            if (realpath(parent::getThumbPath($file_name)) !== false) {
                $thumb_url = parent::getThumbUrl($file_name) . '?t=' . filemtime(parent::getThumbPath($file_name));
            } else {
                $thumb_url = null;
            }


            $arr_files[$key] = [
                'name'      => $file_name,
                'url'       => $file_url,
                'size'      => $file_size,
                'created'   => $file_created,
                'type'      => $file_type,
                'icon'      => $icon,
                'thumb'     => $thumb_url
            ];
        }

        return $arr_files;
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
