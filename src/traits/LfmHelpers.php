<?php

namespace Unisharp\Laravelfilemanager\traits;

use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait LfmHelpers
{
    /*****************************
     ***       Path / Url      ***
     *****************************/

    private $ds = '/';

    public function getThumbPath($image_name = null)
    {
        return $this->getCurrentPath($image_name, 'thumb');
    }

    public function getCurrentPath($file_name = null, $is_thumb = null)
    {
        $path = $this->composeSegments('dir', $is_thumb, $file_name);

        $path = $this->translateToOsPath($path);

        return base_path($path);
    }

    public function getThumbUrl($image_name = null)
    {
        return $this->getFileUrl($image_name, 'thumb');
    }

    public function getFileUrl($image_name = null, $is_thumb = null)
    {
        return url($this->composeSegments('url', $is_thumb, $image_name));
    }

    private function composeSegments($type, $is_thumb, $file_name)
    {
        $full_path = implode($this->ds, [
            $this->getPathPrefix($type),
            $this->getFormatedWorkingDir(),
            $this->appendThumbFolderPath($is_thumb),
            $file_name
        ]);

        $full_path = $this->removeDuplicateSlash($full_path);
        $full_path = $this->translateToLfmPath($full_path);

        return $this->removeLastSlash($full_path);
    }

    public function getPathPrefix($type)
    {
        $default_folder_name = 'files';
        if ($this->isProcessingImages()) {
            $default_folder_name = 'photos';
        }

        $prefix = config('lfm.' . $this->currentLfmType() . 's_folder_name', $default_folder_name);
        $base_directory = config('lfm.base_directory', 'public');

        if ($type === 'dir') {
            $prefix = $base_directory . '/' . $prefix;
        }

        if ($type === 'url' && $base_directory !== 'public') {
            $prefix = 'laravel-filemanager/' . $prefix;
        }

        return $prefix;
    }

    private function getFormatedWorkingDir()
    {
        $working_dir = request('working_dir');

        if (empty($working_dir)) {
            $default_folder_type = 'share';
            if ($this->allowMultiUser()) {
                $default_folder_type = 'user';
            }

            $working_dir = $this->rootFolder($default_folder_type);
        }

        return $this->removeFirstSlash($working_dir);
    }

    private function appendThumbFolderPath($is_thumb)
    {
        if (!$is_thumb) {
            return;
        }

        $thumb_folder_name = config('lfm.thumb_folder_name');
        // if user is inside thumbs folder, there is no need
        // to add thumbs substring to the end of url
        $in_thumb_folder = str_contains($this->getFormatedWorkingDir(), $this->ds . $thumb_folder_name);

        if (!$in_thumb_folder) {
            return $thumb_folder_name . $this->ds;
        }
    }

    public function rootFolder($type)
    {
        if ($type === 'user') {
            $folder_name = $this->getUserSlug();
        } else {
            $folder_name = config('lfm.shared_folder_name');
        }

        return $this->ds . $folder_name;
    }

    public function getRootFolderPath($type)
    {
        return base_path($this->getPathPrefix('dir') . $this->rootFolder($type));
    }

    public function getName($file)
    {
        $lfm_file_path = $this->getInternalPath($file);

        $arr_dir = explode($this->ds, $lfm_file_path);
        $file_name = end($arr_dir);

        return $file_name;
    }

    public function getInternalPath($full_path)
    {
        $full_path = $this->translateToLfmPath($full_path);
        $full_path = $this->translateToUtf8($full_path);
        $lfm_dir_start = strpos($full_path, $this->getPathPrefix('dir'));
        $working_dir_start = $lfm_dir_start + strlen($this->getPathPrefix('dir'));
        $lfm_file_path = $this->ds . substr($full_path, $working_dir_start);

        return $this->removeDuplicateSlash($lfm_file_path);
    }

    private function translateToOsPath($path)
    {
        if ($this->isRunningOnWindows()) {
            $path = str_replace($this->ds, '\\', $path);
        }
        return $path;
    }

    private function translateToLfmPath($path)
    {
        if ($this->isRunningOnWindows()) {
            $path = str_replace('\\', $this->ds, $path);
        }
        return $path;
    }

    private function removeDuplicateSlash($path)
    {
        return str_replace($this->ds . $this->ds, $this->ds, $path);
    }

    private function removeFirstSlash($path)
    {
        if (starts_with($path, $this->ds)) {
            $path = substr($path, 1);
        }

        return $path;
    }

    private function removeLastSlash($path)
    {
        // remove last slash
        if (ends_with($path, $this->ds)) {
            $path = substr($path, 0, -1);
        }

        return $path;
    }

    public function translateFromUtf8($input)
    {
        if ($this->isRunningOnWindows()) {
            $input = iconv('UTF-8', 'BIG5', $input);
        }

        return $input;
    }

    public function translateToUtf8($input)
    {
        if ($this->isRunningOnWindows()) {
            $input = iconv('BIG5', 'UTF-8', $input);
        }

        return $input;
    }


    /****************************
     ***   Config / Settings  ***
     ****************************/

    public function isProcessingImages()
    {
        return lcfirst(str_singular(request('type'))) === 'image';
    }

    public function isProcessingFiles()
    {
        return !$this->isProcessingImages();
    }

    public function currentLfmType()
    {
        $file_type = 'file';
        if ($this->isProcessingImages()) {
            $file_type = 'image';
        }

        return $file_type;
    }

    public function allowMultiUser()
    {
        return config('lfm.allow_multi_user') === true;
    }

    public function allowShareFolder()
    {
        if (!$this->allowMultiUser()) {
            return true;
        }

        return config('lfm.allow_share_folder') === true;
    }

    public function applyIniOverrides()
    {
        if (count(config('lfm.php_ini_overrides')) == 0) {
            return;
        }

        foreach (config('lfm.php_ini_overrides') as $key => $value) {
            if ($value && $value != 'false') {
                ini_set($key, $value);
            }
        }
    }


    /****************************
     ***     File System      ***
     ****************************/

    public function getDirectories($path)
    {
        return array_map(function ($directory) {
            return $this->objectPresenter($directory);
        }, array_filter(File::directories($path), function ($directory) {
            return $this->getName($directory) !== config('lfm.thumb_folder_name');
        }));
    }

    public function getFilesWithInfo($path)
    {
        return array_map(function ($file) {
            return $this->objectPresenter($file);
        }, File::files($path));
    }

    public function objectPresenter($item)
    {
        $item_name = $this->getName($item);
        $is_file = is_file($item);

        if (!$is_file) {
            $file_type = trans('laravel-filemanager::lfm.type-folder');
            $icon = 'fa-folder-o';
            $thumb_url = asset('vendor/laravel-filemanager/img/folder.png');
        } elseif ($this->fileIsImage($item)) {
            $file_type = $this->getFileType($item);
            $icon = 'fa-image';

            $thumb_path = $this->getThumbPath($item_name);
            $file_path = $this->getCurrentPath($item_name);
            if ($this->imageShouldNotHaveThumb($file_path)) {
                $thumb_url = $this->getFileUrl($item_name) . '?timestamp=' . filemtime($file_path);
            } elseif (File::exists($thumb_path)) {
                $thumb_url = $this->getThumbUrl($item_name) . '?timestamp=' . filemtime($thumb_path);
            } else {
                $thumb_url = $this->getFileUrl($item_name) . '?timestamp=' . filemtime($file_path);
            }
        } else {
            $extension = strtolower(File::extension($item_name));
            $file_type = config('lfm.file_type_array.' . $extension) ?: 'File';
            $icon = config('lfm.file_icon_array.' . $extension) ?: 'fa-file';
            $thumb_url = null;
        }

        return (object)[
            'name'    => $item_name,
            'url'     => $is_file ? $this->getFileUrl($item_name) : '',
            'size'    => $is_file ? $this->humanFilesize(File::size($item)) : '',
            'updated' => filemtime($item),
            'path'    => $is_file ? '' : $this->getInternalPath($item),
            'time'    => date("Y-m-d h:m", filemtime($item)),
            'type'    => $file_type,
            'icon'    => $icon,
            'thumb'   => $thumb_url,
            'is_file' => $is_file
        ];
    }

    public function createFolderByPath($path)
    {
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
    }

    public function directoryIsEmpty($directory_path)
    {
        return count(File::allFiles($directory_path)) == 0;
    }

    public function fileIsImage($file)
    {
        $mime_type = $this->getFileType($file);

        return starts_with($mime_type, 'image');
    }

    public function imageShouldNotHaveThumb($file)
    {
        $mine_type = $this->getFileType($file);
        $noThumbType = ['image/gif', 'image/svg+xml'];

        return in_array($mine_type, $noThumbType);
    }

    public function getFileType($file)
    {
        if ($file instanceof UploadedFile) {
            $mime_type = $file->getMimeType();
        } else {
            $mime_type = File::mimeType($file);
        }

        return $mime_type;
    }

    public function sortFilesAndDirectories($arr_items, $sort_type)
    {
        if ($sort_type == 'time') {
            $key_to_sort = 'updated';
        } elseif ($sort_type == 'alphabetic') {
            $key_to_sort = 'name';
        } else {
            $key_to_sort = 'updated';
        }

        uasort($arr_items, function ($a, $b) use ($key_to_sort) {
            return strcmp($a->{$key_to_sort}, $b->{$key_to_sort});
        });

        return $arr_items;
    }


    /****************************
     ***    Miscellaneouses   ***
     ****************************/

    public function getUserSlug()
    {
        if (is_callable(config('lfm.user_field'))) {
            $slug_of_user = call_user_func(config('lfm.user_field'));
        } else {
            $old_slug_of_user = config('lfm.user_field');
            $slug_of_user = empty(auth()->user()) ? '' : auth()->user()->$old_slug_of_user;
        }

        return $slug_of_user;
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
