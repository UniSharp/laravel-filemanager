<?php

namespace UniSharp\LaravelFilemanager;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Http\Request;

class Lfm
{
    const PACKAGE_NAME = 'laravel-filemanager';
    const DS = '/';

    protected $config;
    protected $request;

    public function __construct(Config $config = null, Request $request = null)
    {
        $this->config = $config;
        $this->request = $request;
    }

    public function getStorage($storage_path)
    {
        if ($this->config->get('lfm.driver') == 'storage') {
            return new LfmStorageRepository($storage_path);
        } else {
            return new LfmFileRepository($storage_path);
        }
    }

    public function input($key)
    {
        return $this->request->input($key);
    }

    /**
     * Check current lfm type is image or not.
     *
     * @return bool
     */
    public function isProcessingImages()
    {
        return lcfirst(str_singular($this->input('type'))) === 'image';
    }

    /**
     * Get only the file name.
     *
     * @param  string  $path  Real path of a file.
     * @return string
     */
    public function getNameFromPath($path)
    {
        if (str_contains($path, self::DS)) {
            return substr($path, strrpos($path, self::DS) + 1);
        }

        return $path;
    }

    public function allowFolderType($type)
    {
        if ($type == 'user') {
            return $this->allowMultiUser();
        } else {
            return $this->allowShareFolder();
        }
    }

    public function getCategoryName()
    {
        $type = $this->currentLfmType();

        return $this->config->get('lfm.' . $type . 's_folder_name', [
            'file' => 'files',
            'image' => 'photos',
        ][$type]);
    }

    /**
     * Get current lfm type.
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

    public function getDisplayMode()
    {
        $type_key = $this->currentLfmType();
        $startup_view = config('lfm.' . $type_key . 's_startup_view');

        $view_type = 'grid';
        $target_display_type = $this->input('show_list') ?: $startup_view;

        if (in_array($target_display_type, ['list', 'grid'])) {
            $view_type = $target_display_type;
        }

        return $view_type;
    }

    public function getUserSlug()
    {
        $config = $this->config->get('lfm.user_field');

        if (is_callable($config)) {
            return call_user_func($config);
        }

        if (class_exists($config)) {
            return app()->make($config)->userField();
        }

        return empty(auth()->user()) ? '' : auth()->user()->$config;
    }

    public function getRootFolder($type)
    {
        if ($type === 'user') {
            $folder = $this->getUserSlug();
        } else {
            $folder = $this->config->get('lfm.shared_folder_name');
        }

        return static::DS . $folder;
    }

    public function getThumbFolderName()
    {
        return $this->config->get('lfm.thumb_folder_name');
    }

    public function getFileIcon($ext)
    {
        return $this->config->get("lfm.file_icon_array.{$ext}", 'fa-file-o');
    }

    public function getFileType($ext)
    {
        return $this->config->get("lfm.file_type_array.{$ext}", 'File');
    }

    // TODO: do not use url function, and add test
    public function url($path = '')
    {
        return '/' . $path;
        // return url($path);
    }

    /**
     * Check if users are allowed to use their private folders.
     *
     * @return bool
     */
    public function allowMultiUser()
    {
        return $this->config->get('lfm.allow_multi_user') === true;
    }

    /**
     * Check if users are allowed to use the shared folder.
     * This can be disabled only when allowMultiUser() is true.
     *
     * @return bool
     */
    public function allowShareFolder()
    {
        if (! $this->allowMultiUser()) {
            return true;
        }

        return $this->config->get('lfm.allow_share_folder') === true;
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
     * Check current operating system is Windows or not.
     *
     * @return bool
     */
    public function isRunningOnWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    public function shouldSetStorageRoute()
    {
        $driver = $this->config->get('lfm.driver');

        if ($driver === 'file') {
            return false;
        }

        $storage_root = $this->getStorage('/')->rootPath();

        if ($driver === 'storage' && (ends_with($storage_root, 'public') && ends_with($storage_root, 'public/'))) {
            return false;
        }

        return true;
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
        throw new \Exception(trans(self::PACKAGE_NAME . '::lfm.error-' . $error_type, $variables));
    }
}
