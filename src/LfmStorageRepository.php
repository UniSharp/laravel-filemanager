<?php

namespace UniSharp\LaravelFilemanager;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\Cached\CachedAdapter;

class LfmStorageRepository
{
    private $disk;
    private $path;
    private $helper;

    public function __construct($storage_path, $helper)
    {
        $this->helper = $helper;
        $this->disk = Storage::disk($this->helper->config('disk'));
        $this->path = $storage_path;
    }

    public function __call($function_name, $arguments)
    {
        // TODO: check function exists
        return $this->disk->$function_name($this->path, ...$arguments);
    }

    public function rootPath()
    {
        $adapter = $this->disk->getDriver()->getAdapter();

        if ($adapter instanceof CachedAdapter) {
            $adapter = $adapter->getAdapter();
        }

        return $adapter->getPathPrefix();
    }

    public function move($new_lfm_path)
    {
        return $this->disk->move($this->path, $new_lfm_path->path('storage'));
    }

    public function save($file)
    {
        $nameint = strripos($this->path, "/");
        $nameclean = substr($this->path, $nameint + 1);
        $pathclean = substr_replace($this->path, "", $nameint);
        $this->disk->putFileAs($pathclean, $file, $nameclean);
    }

    public function url($path)
    {
        return $this->disk->url($path);
    }

    public function makeDirectory()
    {
        $this->disk->makeDirectory($this->path, ...func_get_args());

        // some filesystems (e.g. Google Storage, S3?) don't let you set ACLs on directories (because they don't exist)
        // https://cloud.google.com/storage/docs/naming#object-considerations
        if ($this->disk->has($this->path)) {
            $this->disk->setVisibility($this->path, 'public');
        }
    }

    public function extension()
    {
        setlocale(LC_ALL, 'en_US.UTF-8');
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }
}
