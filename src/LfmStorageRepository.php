<?php

namespace UniSharp\LaravelFilemanager;

use Illuminate\Support\Facades\Storage;
use Unisharp\FileApi\FileApi;

class LfmStorageRepository implements RepositoryContract
{
    private $disk_name;

    private $disk;

    private $path;

    public function __construct($storage_path, $disk_name)
    {
        $this->disk_name = $disk_name;
        $this->disk = Storage::disk($this->disk_name);
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

    public function directories()
    {
        return $this->disk->directories($this->path);
    }

    public function files()
    {
        return $this->disk->files($this->path);
    }

    public function makeDirectory()
    {
        return $this->disk->makeDirectory($this->path, 0777, true, true);
    }

    public function exists()
    {
        return $this->disk->exists($this->path);
    }

    public function getFile()
    {
        return $this->disk->get($this->path);
    }

    public function mimeType()
    {
        return $this->disk->mimeType($this->path);
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

    /**
     * Check a folder and its subfolders is empty or not.
     *
     * @param  string  $directory_path  Real path of a directory.
     * @return bool
     */
    public function directoryIsEmpty()
    {
        return count($this->disk->allFiles($this->path)) == 0;
    }

    public function save($file, $new_filename)
    {
        $this->disk->putFileAs($this->path, $file, $new_filename);
    }

    private function insertSuffix($suffix, $file_name)
    {
        return substr_replace($file_name, $suffix, strpos($file_name, '.'), 0);
    }
}
