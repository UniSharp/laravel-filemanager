<?php

namespace Unisharp\Laravelfilemanager;

class LfmStorage
{
    public $disk;

    private $path;

    // TODO: clean DI
    public function __construct(LfmPath $lfm_path, $disk = null)
    {
        $this->disk = $disk;
        $this->path = $lfm_path->path('storage');
    }

    public function __call($function_name, $arguments)
    {
        return $this->disk->$function_name($this->path);
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
}
