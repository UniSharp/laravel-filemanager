<?php

namespace Unisharp\Laravelfilemanager\traits;

use Illuminate\Support\Facades\File;

trait LfmHelpers
{
    /*****************************
     ***       Path / Url      ***
     *****************************/

    public function getThumbPath($image_name = null)
    {
        return $this->getCurrentPath($image_name, 'thumb');
    }

    public function getCurrentPath($file_name = null, $is_thumb = null)
    {
        $path = $this->composeSegments('dir', $is_thumb, $file_name);

        if ($this->isRunningOnWindows()) {
            $path = str_replace('/', '\\', $path);
        }

        return base_path($path);
    }

    public function getThumbUrl($image_name = null)
    {
        return $this->getFileUrl($image_name, 'thumb');
    }

    public function getFileUrl($image_name = null, $is_thumb = null)
    {
        $url = $this->composeSegments('url', $is_thumb, $image_name);

        return $url;
    }

    private function composeSegments($type, $is_thumb, $file_name)
    {
        $full_path = $this->getPathPrefix($type)
            . $this->getFormatedWorkingDir()
            . '/'
            . $this->appendThumbFolderPath($is_thumb)
            . $file_name;

        $full_path = str_replace('\\', '/', $full_path);

        if (ends_with($full_path, '/')) {
            $full_path = substr($full_path, 0, -1);
        }

        return $full_path;
    }

    private function getFormatedWorkingDir()
    {
        $working_dir = request('working_dir');

        // remove first slash
        if (starts_with($working_dir, '/')) {
            $working_dir = substr($working_dir, 1);
        }

        return $working_dir;
    }

    private function appendThumbFolderPath($is_thumb)
    {
        if (!$is_thumb) {
            return;
        }

        $thumb_folder_name = config('lfm.thumb_folder_name');
        //if user is inside thumbs folder there is no need
        // to add thumbs substring to the end of $url
        $in_thumb_folder = preg_match('/'.$thumb_folder_name.'$/i', $this->getFormatedWorkingDir());

        if (!$in_thumb_folder) {
            return $thumb_folder_name . '/';
        }
    }

    public function rootFolder($type)
    {
        $folder_path = '/';

        if ($type === 'user') {
            $folder_path .= $this->getUserSlug();
        } else {
            $folder_path .= config('lfm.shared_folder_name');
        }

        return $folder_path;
    }

    public function getRootFolderPath($type)
    {
        return base_path($this->getPathPrefix('dir') . $this->rootFolder($type));
    }


    /****************************
     ***   Config / Settings  ***
     ****************************/

    public function isProcessingImages()
    {
        return $this->currentLfmType() === 'image';
    }

    public function isProcessingFiles()
    {
        return $this->currentLfmType() === 'file';
    }

    public function currentLfmType($is_for_url = false)
    {
        $file_type = request('type', 'Images');

        if ($is_for_url) {
            return ucfirst($file_type);
        } else {
            return lcfirst(str_singular($file_type));
        }
    }

    public function allowMultiUser()
    {
        return config('lfm.allow_multi_user') === true;
    }

    public function getPathPrefix($type)
    {
        if (in_array($type, ['url', 'dir'])) {
            return config('lfm.' . $this->currentLfmType() . 's_' . $type);
        } else {
            return null;
        }
    }


    /****************************
     ***     File System      ***
     ****************************/

    public function getDirectories($path)
    {
        $thumb_folder_name = config('lfm.thumb_folder_name');
        $all_directories = File::directories($path);

        $arr_dir = [];

        foreach ($all_directories as $directory) {
            $directory_name = $this->getName($directory);

            if ($directory_name !== $thumb_folder_name) {
                $arr_dir[] = (object)[
                    'name' => $directory_name,
                    'path' => $this->getInternalPath($directory)
                ];
            }
        }

        return $arr_dir;
    }

    public function createFolderByPath($path)
    {
        if (!File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }
    }


    /****************************
     ***    Miscellaneouses   ***
     ****************************/

    public function getUserSlug()
    {
        $slug_of_user = config('lfm.user_field');

        return empty(auth()->user()) ? '' : auth()->user()->$slug_of_user;
    }

    public function getName($file)
    {
        $lfm_file_path = $this->getInternalPath($file);

        $arr_dir = explode('/', $lfm_file_path);
        $file_name = end($arr_dir);

        return $file_name;
    }

    public function getInternalPath($file)
    {
        if ($this->isRunningOnWindows()) {
            $file = str_replace('\\', '/', $file);
        }
        $lfm_dir_start = strpos($file, $this->getPathPrefix('dir'));
        $working_dir_start = $lfm_dir_start + strlen($this->getPathPrefix('dir'));
        $lfm_file_path = substr($file, $working_dir_start);

        return str_replace('//', '/', '/' . $lfm_file_path);
    }

    public function error($error_type, $variables = [])
    {
        return trans('laravel-filemanager::lfm.error-' . $error_type, $variables);
    }

    public function humanFilesize($bytes, $decimals = 2)
    {
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
    }

    public function isRunningOnWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}
