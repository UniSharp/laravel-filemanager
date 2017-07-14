<?php

namespace UniSharp\LaravelFilemanager\traits;

use Illuminate\Support\Facades\Storage;
use UniSharp\LaravelFilemanager\LfmPath;
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

    /****************************
     ***   Config / Settings  ***
     ****************************/

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
}
