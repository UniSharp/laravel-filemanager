<?php

namespace Unisharp\Laravelfilemanager\traits;

use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait LfmHelpers
{
    /*****************************
     ***       Path / Url      ***
     *****************************/

    /**
     * Directory separator for url
     *
     * @var string|null
     */
    private $ds = '/';

    /**
     * Get real path of a thumbnail on the operating system.
     *
     * @param  string|null  $image_name  File name of original image
     * @return string|null
     */
    public function getThumbPath($image_name = null)
    {
        return $this->getCurrentPath($image_name, 'thumb');
    }

    /**
     * Get real path of a file, image, or current working directory on the operating system.
     *
     * @param  string|null  $file_name  File name of image or file
     * @return string|null
     */
    public function getCurrentPath($file_name = null, $is_thumb = null)
    {
        $path = $this->composeSegments('dir', $is_thumb, $file_name);

        $path = $this->translateToOsPath($path);

        return base_path($path);
    }

    /**
     * Get url of a thumbnail.
     *
     * @param  string|null  $image_name  File name of original image
     * @return string|null
     */
    public function getThumbUrl($image_name = null)
    {
        return $this->getFileUrl($image_name, 'thumb');
    }

    /**
     * Get url of a original image.
     *
     * @param  string|null  $image_name  File name of original image
     * @return string|null
     */
    public function getFileUrl($image_name = null, $is_thumb = null)
    {
        return url($this->composeSegments('url', $is_thumb, $image_name));
    }

    /**
     * Assemble needed config or input to form url or real path of a file, image, or current working directory.
     *
     * @param  string       $type       Url or dir
     * @param  bollean      $is_thumb   Image is a thumbnail or not
     * @param  string|null  $file_name  File name of image or file
     * @return string|null
     */
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

    /**
     * Assemble base_directory and route prefix config.
     *
     * @param  string  $type  Url or dir
     * @return string
     */
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
            $prefix = config('lfm.prefix', 'laravel-filemanager') . '/' . $prefix;
        }

        return $prefix;
    }

    /**
     * Get current or default working directory.
     *
     * @return string
     */
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

    /**
     * Get thumbnail folder name.
     *
     * @return string|null
     */
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

    /**
     * Get root working directory.
     *
     * @param  string  $type  User or share.
     * @return string
     */
    public function rootFolder($type)
    {
        if ($type === 'user') {
            $folder_name = $this->getUserSlug();
        } else {
            $folder_name = config('lfm.shared_folder_name');
        }

        return $this->ds . $folder_name;
    }

    /**
     * Get real path of root working directory on the operating system.
     *
     * @param  string|null  $type  User or share
     * @return string|null
     */
    public function getRootFolderPath($type)
    {
        return base_path($this->getPathPrefix('dir') . $this->rootFolder($type));
    }

    /**
     * Get only the file name.
     *
     * @param  string  $file  Real path of a file.
     * @return string
     */
    public function getName($file)
    {
        $lfm_file_path = $this->getInternalPath($file);

        $arr_dir = explode($this->ds, $lfm_file_path);
        $file_name = end($arr_dir);

        return $file_name;
    }

    /**
     * Get url with only working directory and file name.
     *
     * @param  string  $full_path  Real path of a file.
     * @return string
     */
    public function getInternalPath($full_path)
    {
        $full_path = $this->translateToLfmPath($full_path);
        $full_path = $this->translateToUtf8($full_path);
        $lfm_dir_start = strpos($full_path, $this->getPathPrefix('dir'));
        $working_dir_start = $lfm_dir_start + strlen($this->getPathPrefix('dir'));
        $lfm_file_path = $this->ds . substr($full_path, $working_dir_start);

        return $this->removeDuplicateSlash($lfm_file_path);
    }

    /**
     * Change directiry separator, from url one to one on current operating system.
     *
     * @param  string  $path  Url of a file.
     * @return string
     */
    private function translateToOsPath($path)
    {
        if ($this->isRunningOnWindows()) {
            $path = str_replace($this->ds, '\\', $path);
        }
        return $path;
    }

    /**
     * Change directiry separator, from one on current operating system to url one.
     *
     * @param  string  $path  Real path of a file.
     * @return string
     */
    private function translateToLfmPath($path)
    {
        if ($this->isRunningOnWindows()) {
            $path = str_replace('\\', $this->ds, $path);
        }
        return $path;
    }

    /**
     * Strip duplicate slashes from url.
     *
     * @param  string  $path  Any url.
     * @return string
     */
    private function removeDuplicateSlash($path)
    {
        return str_replace($this->ds . $this->ds, $this->ds, $path);
    }

    /**
     * Strip first slash from url.
     *
     * @param  string  $path  Any url.
     * @return string
     */
    private function removeFirstSlash($path)
    {
        if (starts_with($path, $this->ds)) {
            $path = substr($path, 1);
        }

        return $path;
    }

    /**
     * Strip last slash from url.
     *
     * @param  string  $path  Any url.
     * @return string
     */
    private function removeLastSlash($path)
    {
        // remove last slash
        if (ends_with($path, $this->ds)) {
            $path = substr($path, 0, -1);
        }

        return $path;
    }

    /**
     * Translate file name to make it compatible on Windows.
     *
     * @param  string  $input  Any string.
     * @return string
     */
    public function translateFromUtf8($input)
    {
        if ($this->isRunningOnWindows()) {
            $input = iconv('UTF-8', mb_detect_encoding($input), $input);
        }

        return $input;
    }

    /**
     * Translate file name from Windows.
     *
     * @param  string  $input  Any string.
     * @return string
     */
    public function translateToUtf8($input)
    {
        if ($this->isRunningOnWindows()) {
            $input = iconv(mb_detect_encoding($input), 'UTF-8', $input);
        }

        return $input;
    }


    /****************************
     ***   Config / Settings  ***
     ****************************/

    /**
     * Check current lfm type is image or not.
     *
     * @return boolean
     */
    public function isProcessingImages()
    {
        return lcfirst(str_singular(request('type'))) === 'image';
    }

    /**
     * Check current lfm type is file or not.
     *
     * @return boolean
     */
    public function isProcessingFiles()
    {
        return !$this->isProcessingImages();
    }

    /**
     * Get current lfm type..
     *
     * @return string
     */
    public function currentLfmType()
    {
        $file_type = 'file';
        if ($this->isProcessingImages()) {
            $file_type = 'image';
        }

        return $file_type;
    }

    /**
     * Check if users are allowed to use their private folders.
     *
     * @return boolean
     */
    public function allowMultiUser()
    {
        return config('lfm.allow_multi_user') === true;
    }

    /**
     * Check if users are allowed to use the shared folder.
     * This can be disabled only when allowMultiUser() is true.
     *
     * @return boolean
     */
    public function allowShareFolder()
    {
        if (!$this->allowMultiUser()) {
            return true;
        }

        return config('lfm.allow_share_folder') === true;
    }

    /**
     * Overrides settings in php.ini.
     *
     * @return null
     */
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

    /**
     * Get folders by the given directory.
     *
     * @param  string  $path  Real path of a directory.
     * @return array of objects
     */
    public function getDirectories($path)
    {
        return array_map(function ($directory) {
            return $this->objectPresenter($directory);
        }, array_filter(File::directories($path), function ($directory) {
            return $this->getName($directory) !== config('lfm.thumb_folder_name');
        }));
    }

    /**
     * Get files by the given directory.
     *
     * @param  string  $path  Real path of a directory.
     * @return array of objects
     */
    public function getFilesWithInfo($path)
    {
        return array_map(function ($file) {
            return $this->objectPresenter($file);
        }, File::files($path));
    }

    /**
     * Format a file or folder to object.
     *
     * @param  string  $item  Real path of a file or directory.
     * @return object
     */
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

    /**
     * Create folder if not exist.
     *
     * @param  string  $path  Real path of a directory.
     * @return null
     */
    public function createFolderByPath($path)
    {
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
    }

    /**
     * Check a folder and its subfolders is empty or not.
     *
     * @param  string  $directory_path  Real path of a directory.
     * @return boolean
     */
    public function directoryIsEmpty($directory_path)
    {
        return count(File::allFiles($directory_path)) == 0;
    }

    /**
     * Check a file is image or not.
     *
     * @param  mixed  $file  Real path of a file or instance of UploadedFile.
     * @return boolean
     */
    public function fileIsImage($file)
    {
        $mime_type = $this->getFileType($file);

        return starts_with($mime_type, 'image');
    }

    /**
     * Check thumbnail should be created when the file is uploading.
     *
     * @param  mixed  $file  Real path of a file or instance of UploadedFile.
     * @return boolean
     */
    public function imageShouldNotHaveThumb($file)
    {
        $mine_type = $this->getFileType($file);
        $noThumbType = ['image/gif', 'image/svg+xml'];

        return in_array($mine_type, $noThumbType);
    }

    /**
     * Get mime type of a file.
     *
     * @param  mixed  $file  Real path of a file or instance of UploadedFile.
     * @return string
     */
    public function getFileType($file)
    {
        if ($file instanceof UploadedFile) {
            $mime_type = $file->getMimeType();
        } else {
            $mime_type = File::mimeType($file);
        }

        return $mime_type;
    }

    /**
     * Sort files and directories.
     *
     * @param  mixed  $arr_items  Array of files or folders or both.
     * @param  mixed  $sort_type  Alphabetic or time.
     * @return array of object
     */
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

    /**
     * Get the name of private folder of current user.
     *
     * @return string
     */
    public function getUserSlug()
    {
        if (is_callable(config('lfm.user_field'))) {
            $slug_of_user = call_user_func(config('lfm.user_field'));
        } elseif (class_exists(config('lfm.user_field'))) {
            $config_handler = config('lfm.user_field');
            $slug_of_user = app()->make($config_handler)->userField();
        } else {
            $old_slug_of_user = config('lfm.user_field');
            $slug_of_user = empty(auth()->user()) ? '' : auth()->user()->$old_slug_of_user;
        }

        return $slug_of_user;
    }

    /**
     * Shorter function of getting localized error message..
     *
     * @param  mixed  $error_type  Key of message in lang file.
     * @param  mixed  $variables   Variables the message needs.
     * @return string
     */
    public function error($error_type, $variables = [])
    {
        return trans('laravel-filemanager::lfm.error-' . $error_type, $variables);
    }

    /**
     * Make file size readable.
     *
     * @param  integer  $bytes     File size in bytes.
     * @param  integer  $decimals  Decimals.
     * @return string
     */
    public function humanFilesize($bytes, $decimals = 2)
    {
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
    }

    /**
     * Check current operating system is Windows or not.
     *
     * @return boolean
     */
    public function isRunningOnWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}
