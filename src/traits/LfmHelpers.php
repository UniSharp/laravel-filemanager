<?php

namespace Unisharp\Laravelfilemanager\traits;

use Illuminate\Support\Facades\Storage;
use Unisharp\Laravelfilemanager\LfmPath;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait LfmHelpers
{
    /*****************************
     ***       Path / Url      ***
     *****************************/

    /**
     * Directory separator for url.
     *
     * @var string|null
     */
    private $ds = '/';

    protected $package_name = 'laravel-filemanager';

    private $disk_name = 'local'; // config('lfm.disk')

    public $disk_root;

    public $disk;

    public function initHelper()
    {
        $this->disk = Storage::disk($this->disk_name);
        $this->disk_root = config('filesystems.disks.' . $this->disk_name . '.root');
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
     * Get only the file name.
     *
     * @param  string  $file  Real path of a file.
     * @return string
     */
    public function getName($file)
    {
        return substr($file, strrpos($file, $this->ds) + 1);
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

    /****************************
     ***   Config / Settings  ***
     ****************************/

    /**
     * Check current lfm type is image or not.
     *
     * @return bool
     */
    public function isProcessingImages()
    {
        return lcfirst(str_singular(request('type'))) === 'image';
    }

    /**
     * Check current lfm type is file or not.
     *
     * @return bool
     */
    public function isProcessingFiles()
    {
        return ! $this->isProcessingImages();
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

    public function allowFolderType($type)
    {
        if ($type == 'user') {
            return $this->allowMultiUser();
        } else {
            return $this->allowShareFolder();
        }
    }

    /**
     * Check if users are allowed to use their private folders.
     *
     * @return bool
     */
    private function allowMultiUser()
    {
        return config('lfm.allow_multi_user') === true;
    }

    /**
     * Check if users are allowed to use the shared folder.
     * This can be disabled only when allowMultiUser() is true.
     *
     * @return bool
     */
    private function allowShareFolder()
    {
        if (! $this->allowMultiUser()) {
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

    public function getStoragePath($path)
    {
        return str_replace($this->disk_root . '/', '', $path);
    }

    /**
     * Check a file is image or not.
     *
     * @param  mixed  $file  Real path of a file or instance of UploadedFile.
     * @return bool
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
     * @return bool
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
            $mime_type = $this->disk->mimeType($this->getStoragePath($file));
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
    public function sortByColumn($arr_items, $key_to_sort)
    {
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
        return trans($this->package_name . '::lfm.error-' . $error_type, $variables);
    }

    /**
     * Check current operating system is Windows or not.
     *
     * @return bool
     */
    public function isRunningOnWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}
