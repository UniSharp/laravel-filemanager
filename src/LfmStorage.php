<?php

namespace Unisharp\Laravelfilemanager;

use Illuminate\Support\Facades\Storage;

class LfmStorage
{
    private $disk_name = 'local'; // config('lfm.disk')

    public $disk_root;

    public $disk;

    public $lfm;

    // TODO: clean DI
    public function __construct($disk = null, $root = null, Lfm $lfm = null)
    {
        $this->disk = $disk ?: Storage::disk($this->disk_name);
        $this->disk_root = $root ?: config('filesystems.disks.' . $this->disk_name . '.root');
        $this->lfm = $lfm;
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
     * @return bool
     */
    public function createFolder($storage_path)
    {
        if (! $this->disk->exists($storage_path)) {
            return $this->disk->makeDirectory($storage_path, 0777, true, true);
        }

        return false;
    }

    public function exists($storage_path)
    {
        return $this->disk->exists($storage_path);
    }

    public function get($storage_path)
    {
        return new LfmItem($this, $storage_path);
    }

    public function getFile($storage_path)
    {
        return $this->disk->get($storage_path);
    }

    public function mimeType($storage_path)
    {
        return $this->disk->mimeType($storage_path);
    }
}
