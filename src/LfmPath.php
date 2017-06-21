<?php

namespace Unisharp\Laravelfilemanager;

use Storage;

class LfmPath
{
    private $ds = '/';

    protected $package_name = 'laravel-filemanager';

    private $working_dir;
    private $full_path;
    private $is_thumb = false;
    private $storage;

    public function __construct()
    {
        $this->storage = new LfmStorage;
    }

    public function dir($working_dir)
    {
        $this->working_dir = $working_dir;

        return $this;
    }

    public function thumb()
    {
        $this->is_thumb = true;

        return $this;
    }

    public function path($type = 'storage', $item_name = null) // full, storage, working_dir
    {
        $this->working_dir = $this->working_dir ?: request('working_dir');

        if (empty($this->working_dir)) {
            $default_folder_type = 'share';
            if ($this->allowFolderType('user')) {
                $default_folder_type = 'user';
            }

            $this->working_dir = $this->rootFolder($default_folder_type);
        }

        $this->full_path = base_path($this->getPathPrefix() . $this->working_dir);

        if ($type == 'storage') {
            $result = str_replace($this->storage->disk_root . $this->ds, '', $this->full_path);
        } elseif ($type == 'working_dir') {
            $result = $this->working_dir;
        } else {
            $result = $this->full_path;
        }

        if ($this->is_thumb) {
            $result .= $this->ds . config('lfm.thumb_folder_name');
        }

        if ($item_name) {
            $result .= $this->ds . $item_name;
        }

        return $result;
    }

    public function url($item_name, $with_timestamp = false)
    {
        $prefix = config('lfm.url_prefix', $this->package_name);

        $default_folder_name = 'files';
        if ($this->isProcessingImages()) {
            $default_folder_name = 'photos';
        }

        $category_name = config('lfm.' . $this->currentLfmType() . 's_folder_name', $default_folder_name);

        $this->working_dir = $this->working_dir ?: request('working_dir');

        if (empty($this->working_dir)) {
            $default_folder_type = 'share';
            if ($this->allowFolderType('user')) {
                $default_folder_type = 'user';
            }

            $this->working_dir = $this->rootFolder($default_folder_type);
        }

        $result = $prefix . $this->ds . $category_name . $this->working_dir;

        if ($this->is_thumb) {
            $result .= $this->ds . config('lfm.thumb_folder_name');
        }

        return url($result . $this->ds . $item_name);
    }

    public function folders()
    {
        $storage_path = $this->path('storage');

        return array_filter($this->storage->directories($storage_path), function ($directory) {
            return $directory->name !== config('lfm.thumb_folder_name');
        });
    }

    public function files()
    {
        $storage_path = $this->path('storage');

        return $this->storage->files($storage_path);
    }

    public function exists($item_name)
    {
        $storage_path = $this->path('storage', $item_name);

        return $this->storage->exists($storage_path);
    }

    public function get($item_name)
    {
        $storage_path = $this->path('storage', $item_name);

        return $this->storage->get($storage_path);
    }

    public function createFolder($item_name)
    {
        $storage_path = $this->path('storage', $item_name);

        return $this->storage->createFolder($storage_path);
    }

    private function reset()
    {
        $this->working_dir = null;
        $this->full_path = null;
    }

    /**
     * Assemble base_directory and route prefix config.
     *
     * @param  string  $type  Url or dir
     * @return string
     */
    private function getPathPrefix()
    {
        $default_folder_name = 'files';
        if ($this->isProcessingImages()) {
            $default_folder_name = 'photos';
        }

        $category_name = config('lfm.' . $this->currentLfmType() . 's_folder_name', $default_folder_name);

        $prefix = $this->storage->disk_root . $this->ds . $this->package_name;
        $prefix = str_replace(base_path() . $this->ds, '', $prefix);

        return $prefix . $this->ds . $category_name;
    }

    /**
     * Check current lfm type is image or not.
     *
     * @return bool
     */
    private function isProcessingImages()
    {
        return lcfirst(str_singular(request('type'))) === 'image';
    }

    /**
     * Get current lfm type..
     *
     * @return string
     */
    private function currentLfmType()
    {
        $file_type = 'file';
        if ($this->isProcessingImages()) {
            $file_type = 'image';
        }

        return $file_type;
    }

    /**
     * Get root working directory.
     *
     * @param  string  $type  User or share.
     * @return string
     */
    private function rootFolder($type)
    {
        if ($type === 'user') {
            $folder_name = $this->getUserSlug();
        } else {
            $folder_name = config('lfm.shared_folder_name');
        }

        return $this->ds . $folder_name;
    }

    /**
     * Get the name of private folder of current user.
     *
     * @return string
     */
    private function getUserSlug()
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
}
