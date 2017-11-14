<?php

namespace UniSharp\LaravelFilemanager;

use Illuminate\Support\Facades\File;

class LfmFileRepository implements RepositoryContract
{
    private $path;

    public function __construct($storage_path)
    {
        $this->path = $this->rootPath() . $storage_path;
    }

    public function __call($function_name, $arguments)
    {
        return File::$function_name($this->path, ...$arguments);
    }

    // TODO: check ending with slash in tests
    public function rootPath()
    {
        return public_path() . '/';
    }

    public function directories()
    {
        return File::directories($this->path);
    }

    public function files()
    {
        return File::files($this->path);
    }

    public function makeDirectory()
    {
        return File::makeDirectory($this->path, 0777, true, true);
    }

    public function exists()
    {
        return File::exists($this->path);
    }

    public function getFile()
    {
        return File::get($this->path);
    }

    public function mimeType()
    {
        return File::mimeType($this->path);
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
        return File::move($this->path, $new_lfm_path->path('storage'));
    }

    /**
     * Check a folder and its subfolders is empty or not.
     *
     * @param  string  $directory_path  Real path of a directory.
     * @return bool
     */
    public function directoryIsEmpty()
    {
        return count(File::allFiles($this->path)) == 0;
    }

    public function save($file, $new_filename)
    {
        $result_filename = $new_filename . '.' . $file->getClientOriginalExtension();
        $new_filepath = $this->path . '/' . $result_filename;
        File::move($file->getRealPath(), $new_filepath);

        \Log::info($result_filename);

        return $result_filename;
    }
}
