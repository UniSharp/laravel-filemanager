<?php

namespace Unisharp\Laravelfilemanager\traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;

trait LfmHelpers
{
    protected $file_location = null;
    protected $dir_location = null;

    public function __construct()
    {
        $this->file_type = Input::get('type', 'Images'); // default set to Images.

        if ($this->isProcessingImages()) {
            $this->dir_location = Config::get('lfm.images_url');
            $this->file_location = Config::get('lfm.images_dir');
        } elseif ($this->isProcessingFiles()) {
            $this->dir_location = Config::get('lfm.files_url');
            $this->file_location = Config::get('lfm.files_dir');
        } else {
            throw new \Exception('unexpected type parameter');
        }
    }

    /*****************************
     ***   Private Functions   ***
     *****************************/
    private function formatLocation($location, $type = null, $get_thumb = false)
    {
        if ($type === 'share') {
            return $location . Config::get('lfm.shared_folder_name');

        } elseif ($type === 'user') {
            return $location . $this->getUserSlug();

        }

        $working_dir = Input::get('working_dir');

        // remove first slash
        if (substr($working_dir, 0, 1) === '/') {
            $working_dir = substr($working_dir, 1);
        }


        $location .= $working_dir;

        if ($type === 'directory' || $type === 'thumb') {
            $location .= '/';
        }

        //if user is inside thumbs folder there is no need
        // to add thumbs substring to the end of $location
        $in_thumb_folder = preg_match('/'.Config::get('lfm.thumb_folder_name').'$/i',$working_dir);

        if ($type === 'thumb' && !$in_thumb_folder) {
            $location .= Config::get('lfm.thumb_folder_name') . '/';
        }

        return $location;
    }


    /****************************
     ***   Shared Functions   ***
     ****************************/


    public function getUserSlug()
    {
        return empty(auth()->user()) ? '' : \Auth::user()->user_field;
    }


    public function getPath($type = null, $get_thumb = false)
    {
        $path = base_path() . '/' . $this->file_location;

        $path = $this->formatLocation($path, $type);

        return $path;
    }


    public function getUrl($type = null)
    {
        $url = $this->dir_location;

        $url = $this->formatLocation($url, $type);

        $url = str_replace('\\','/',$url);

        return $url;
    }


    public function getDirectories($path)
    {
        $thumb_folder_name = Config::get('lfm.thumb_folder_name');
        $all_directories = File::directories($path);

        $arr_dir = [];

        foreach ($all_directories as $directory) {
            $dir_name = $this->getFileName($directory);

            if ($dir_name['short'] !== $thumb_folder_name) {
                $arr_dir[] = $dir_name;
            }
        }

        return $arr_dir;
    }


    public function getFileName($file)
    {
        $lfm_dir_start = strpos($file, $this->file_location);
        $working_dir_start = $lfm_dir_start + strlen($this->file_location);
        $lfm_file_path = substr($file, $working_dir_start);

        $arr_dir = explode('/', $lfm_file_path);
        $arr_filename['short'] = end($arr_dir);
        $arr_filename['long'] = '/' . $lfm_file_path;

        return $arr_filename;
    }

    public function isProcessingImages()
    {
        return $this->currentProcessingType() === 'image';
    }

    public function isProcessingFiles()
    {
        return $this->currentProcessingType() === 'file';
    }

    public function currentProcessingType($is_for_url = false)
    {
        $file_type = Input::get('type', 'Images');

        if ($is_for_url) {
            return ucfirst($file_type);
        } else {
            return lcfirst(str_singular($file_type));
        }
    }
}
