<?php

namespace Unisharp\Laravelfilemanager;

use Storage;

class LfmStorage
{
    private $disk_name = 'local'; // config('lfm.disk')

    public $disk_root;

    public $disk;

    public function __construct()
    {
        $this->disk = Storage::disk($this->disk_name);
        $this->disk_root = config('filesystems.disks.' . $this->disk_name . '.root');
    }

    public function directories($storage_path)
    {
        return array_map(function ($directory) {
            return $this->get($directory);
        }, $this->disk->directories($storage_path));
    }

    public function files($storage_path)
    {
        return array_map(function ($file_name) {
            return $this->get($file_name);
        }, $this->disk->files($storage_path));
    }

    /**
     * Create folder if not exist.
     *
     * @param  string  $path  Real path of a directory.
     * @return null
     */
    public function createFolder($storage_path)
    {
        if (! $this->disk->exists($storage_path)) {
            $this->disk->makeDirectory($storage_path, 0777, true, true);
        }
    }

    public function exists($storage_path)
    {
        return $this->disk->exists($storage_path);
    }

    public function get($storage_path)
    {
        return new LfmItem($storage_path);
    }
}
