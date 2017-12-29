<?php

namespace UniSharp\LaravelFilemanager;

use Illuminate\Support\Facades\Storage;

class LfmStorageRepository implements RepositoryContract
{
    private $disk;

    private $path;

    public function __construct($storage_path, $disk_name)
    {
        $this->disk = Storage::disk($disk_name);
        $this->path = $storage_path;
    }

    public function __call($function_name, $arguments)
    {
        // TODO: check function exists
        return $this->disk->$function_name($this->path, ...$arguments);
    }

    public function rootPath()
    {
        // storage_path('app')
        return $this->disk->getDriver()->getAdapter()->getPathPrefix();
    }

    public function isDirectory()
    {
        $parent_path = substr($this->path, 0, strrpos($this->path, '/'));
        $current_path = $this->path;
        $this->path = $parent_path;

        return in_array($current_path, $this->directories());
    }

    public function move($new_lfm_path)
    {
        return $this->disk->move($this->path, $new_lfm_path->path('storage'));
    }

    public function save($file, $new_filename)
    {
        $this->disk->putFileAs($this->path, $file, $new_filename);
    }

    public function url($path)
    {
        return $this->disk->url($path);
    }

    public function makeDirectory($mode, $recursive, $force)
    {
        $this->disk->makeDirectory($this->path, $mode, $recursive, $force);

        $this->disk->setVisibility($this->path, 'public');
    }
}
