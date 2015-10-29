<?php namespace Unisharp\Laravelfilemanager\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;

/**
 * Class ItemsController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class ItemsController extends LfmController {

    /**
     * Return json list of files
     *
     * @return mixed
     */
    public function getFiles()
    {
        $path = $this->file_location;

        if (Input::has('base')) {
            $path .= Input::get('base');
        }

        $files = File::files(base_path($path));

        $file_info = $this->getFileInfos($files, 'Files');

        $directories = parent::getDirectories($path);

        if (Input::get('show_list') == 1) {
            $view = 'laravel-filemanager::files-list';
        } else {
            $view = 'laravel-filemanager::files';
        }

        return View::make($view)
            ->with('files', $files)
            ->with('file_info', $file_info)
            ->with('directories', $directories)
            ->with('base', Input::get('base'))
            ->with('dir_location', $this->file_location);
    }


    /**
     * Get the images to load for a selected folder
     *
     * @return mixed
     */
    public function getImages()
    {
        $path = $this->file_location;

        if (Input::has('base')) {
            $path .= Input::get('base');
        }

        $files = File::files(base_path($path));

        $file_info = $this->getFileInfos($files, 'Images');

        $directories = parent::getDirectories($path);

        if (Input::get('show_list') == 1) {
            $view = 'laravel-filemanager::images-list';
        } else {
            $view = 'laravel-filemanager::images';
        }

        return View::make($view)
            ->with('files', $files)
            ->with('file_info', $file_info)
            ->with('directories', $directories)
            ->with('base', Input::get('base'))
            ->with('dir_location', $this->dir_location);
    }
    

    private function getFileInfos($files, $type = 'Images')
    {
        $file_info = [];

        foreach ($files as $key => $file) {
            $path_parts = explode('/', $file);
            $file_name = end($path_parts);
            $file_created = filemtime($file);

            $file_size = number_format((File::size($file) / 1024), 2, ".", "");
            if ($file_size > 1024) {
                $file_size = number_format(($file_size / 1024), 2, ".", "") . " Mb";
            } else {
                $file_size = $file_size . " Kb";
            }

            if ($type === 'Images') {
                $file_type = File::mimeType($file);
                $icon = '';
            } else {
                $extension = strtolower(File::extension($file_name));

                $icon_array = Config::get('lfm.file_icon_array');
                $type_array = Config::get('lfm.file_type_array');

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

}
