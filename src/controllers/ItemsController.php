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

        $directories = $this->getDirectories($path, 'Files');

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

        $directories = $this->getDirectories($path, 'Images');

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


    private function getDirectories($path, $type = 'Images')
    {
        $all_directories = File::directories(base_path($path));

        $directories = [];

        foreach ($all_directories as $directory) {
            if ($type !== 'Files' && basename($directory) !== 'thumbs') {
                $directories[] = basename($directory);
            }
        }

        return $directories;
    }
    

    private function getFileInfos($files, $type = 'Images')
    {
        $file_info = [];

        foreach ($files as $file) {
            $file_name = $file;
            $file_created = filemtime($file);

            if ($type === 'Images') {
                $file_size = number_format((Image::make($file)->filesize() / 1024), 2, ".", "");
                if ($file_size > 1000) {
                    $file_size = number_format((Image::make($file)->filesize() / 1024), 2, ".", "") . " Mb";
                } else {
                    $file_size = $file_size . " Kb";
                }
                $file_type = Image::make($file)->mime();
                $extension = '';
                $icon = '';
            } else {
                $file_size = 1;
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

            $file_info[] = [
                'name'      => $file_name,
                'size'      => $file_size,
                'created'   => $file_created,
                'type'      => $file_type,
                'extension' => $extension,
                'icon'      => $icon,
            ];
        }

        return $file_info;
    }

}
