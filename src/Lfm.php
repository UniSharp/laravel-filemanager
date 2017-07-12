<?php

namespace UniSharp\LaravelFilemanager;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Http\Request;

class Lfm
{
    const PACKAGE_NAME = 'laravel-filemanager';

    const DS = '/';

    protected $config;
    protected $storage;
    protected $request;

    public function __construct(Config $config, Request $request = null)
    {
        $this->config = $config;
        $this->request = $request;
    }

    public function setStorage(LfmStorage $storage)
    {
        $this->storage = $storage;

        return $this;
    }

    public function getStorage()
    {
        return $this->storage ?: app(LfmStorage::class);
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

    public function getNameFromPath($path)
    {
        $segments = explode(self::DS, $path);
        return end($segments);
    }

    public function allowFolderType($type)
    {
        if ($type == 'user') {
            return $this->allowMultiUser();
        } else {
            return $this->allowShareFolder();
        }
    }

    public function getCategoryName($type)
    {
        return $this->config->get('lfm.' . $type . 's_folder_name', [
            'file' => 'files',
            'image' => 'photos',
        ][$type]);
    }

    // TODO: test
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

    public function getUrlPrefix()
    {
        return $this->config->get('lfm.url_prefix', static::PACKAGE_NAME);
    }

    public function getThumbFolderName()
    {
        return $this->config->get('lfm.thumb_folder_name');
    }

    public function getFileIcon($ext)
    {
        return $this->config->get("lfm.file_icon_array.{$ext}", 'fa-file');
    }

    public function getFileType($ext)
    {
        return $this->config->get("lfm.file_type_array.{$ext}", 'File');
    }

    // TODO: do not use base_path function, and add test
    public function basePath($path = '')
    {
        return base_path($path);
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
    private function allowMultiUser()
    {
        return $this->config->get('lfm.allow_multi_user') === true;
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

        return $this->config->get('lfm.allow_share_folder') === true;
    }
}
