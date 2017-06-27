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

    private $lfm;

    public function initHelper()
    {
        $this->disk = Storage::disk($this->disk_name);
        $this->disk_root = config('filesystems.disks.' . $this->disk_name . '.root');
        // $this->lfm = new LfmPath;
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

        $category_name = config('lfm.' . $this->currentLfmType() . 's_folder_name', $default_folder_name);

        if ($type === 'dir') {
            $prefix = $this->disk_root . '/' . $this->package_name;
            $prefix = str_replace(base_path() . '/', '', $prefix);
        }

        if ($type === 'url') {
            $prefix = config('lfm.url_prefix', $this->package_name);
        }

        return $prefix . '/' . $category_name;
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

    /**
     * Create folder if not exist.
     *
     * @param  string  $path  Real path of a directory.
     * @return null
     */
    public function createFolderByPath($path)
    {
        if (! $this->exists($path)) {
            $this->disk->makeDirectory($this->getStoragePath($path), 0777, true, true);
        }
    }

    public function getStoragePath($path)
    {
        return str_replace($this->disk_root . '/', '', $path);
    }

    public function getFullPath($storage_path)
    {
        return $this->disk_root . $this->ds . $storage_path;
    }

    /**
     * Check a folder and its subfolders is empty or not.
     *
     * @param  string  $directory_path  Real path of a directory.
     * @return bool
     */
    public function directoryIsEmpty($directory_path)
    {
        return count($this->disk->allFiles($this->getStoragePath($directory_path))) == 0;
    }

    public function exists($full_path)
    {
        return $this->disk->exists($this->getStoragePath($full_path));
    }

    public function delete($full_path)
    {
        return $this->disk->delete($this->getStoragePath($full_path));
    }

    public function deleteDirectory($full_path)
    {
        return $this->disk->deleteDirectory($this->getStoragePath($full_path));
    }

    public function getFile($storage_path)
    {
        return $this->disk->get($storage_path);
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

    public function isDirectory($path)
    {
        $path = $this->getStoragePath($path);
        $directory_path = substr($path, 0, strrpos($path, $this->ds));
        $directory_name = $this->getName($path);

        return in_array($path, $this->disk->directories($directory_path));
    }

    public function move($old_file, $new_file)
    {
        $this->disk->move($this->getStoragePath($old_file), $this->getStoragePath($new_file));
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
     * Make file size readable.
     *
     * @param  int  $bytes     File size in bytes.
     * @param  int  $decimals  Decimals.
     * @return string
     */
    public function humanFilesize($bytes, $decimals = 2)
    {
        $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
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
