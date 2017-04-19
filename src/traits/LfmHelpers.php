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

    public function enabledShareFolder()
    {
        return config('lfm.allow_share_folder') === true;
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
                    'name'    => $directory_name,
                    'updated' => filemtime($directory),
                    'path'    => $this->getInternalPath($directory),
                    'time'    => date("Y-m-d h:m", filemtime($directory)),
                    'type'    => trans('laravel-filemanager::lfm.type-folder'),
                    'icon'    => 'fa-folder-o',
                    'thumb'   => asset('vendor/laravel-filemanager/img/folder.png'),
                    'is_file' => false
                ];
            }
        }

        return $arr_dir;
    }

    public function getFilesWithInfo($path)
    {
        $arr_files = [];

        foreach (File::files($path) as $key => $file) {
            $file_name = $this->getName($file);

            if ($this->fileIsImage($file)) {
                $file_type = $this->getFileType($file);
                $icon = 'fa-image';
            } else {
                $extension = strtolower(File::extension($file_name));
                $file_type = config('lfm.file_type_array.' . $extension) ?: 'File';
                $icon = config('lfm.file_icon_array.' . $extension) ?: 'fa-file';
            }

            $thumb_path = $this->getThumbPath($file_name);
            $file_path = $this->getCurrentPath($file_name);
            if (File::exists($thumb_path)) {
                $thumb_url = $this->getThumbUrl($file_name) . '?timestamp=' . filemtime($thumb_path);
            } elseif ($this->isValidImageType($file_path)) {
                $thumb_url = $this->getFileUrl($file_name) . '?timestamp=' . filemtime($file_path);
            } else {
                $thumb_url = null;
            }


            $arr_files[$key] = (object)[
                'name'      => $file_name,
                'url'       => $this->getFileUrl($file_name),
                'size'      => $this->humanFilesize(File::size($file)),
                'updated'   => filemtime($file),
                'time'      => date("Y-m-d h:m", filemtime($file)),
                'type'      => $file_type,
                'icon'      => $icon,
                'thumb'     => $thumb_url,
                'is_file'   => true
            ];
        }

        return $arr_files;
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

    public function isImageToThumb($file)
    {
        $mine_type = $this->getFileType($file);
        $noThumbType = ['image/gif', 'image/svg+xml'];

        if (in_array($mine_type, $noThumbType)) {
            return false;
        }

        return true;
    }

    public function isValidImageType($file)
    {
        $mine_type = $this->getFileType($file);
        $valid_image_mimetypes = config('lfm.valid_image_mimetypes');

        if (in_array($mine_type, $valid_image_mimetypes)) {
            return true;
        }

        return false;
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
        } elseif ($sort_type == 'alpha') {
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
