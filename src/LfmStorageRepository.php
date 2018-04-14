<?php

namespace UniSharp\LaravelFilemanager;

use Illuminate\Support\Facades\Storage;

class LfmStorageRepository implements RepositoryContract
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
        // storage_path('app')
        return $this->disk->getDriver()->getAdapter()->getPathPrefix();
    }

    public function move($new_lfm_path)
    {
        return $this->disk->move($this->path, $new_lfm_path->path('storage'));
    }

    public function save($file_content)
    {
	    $nameint=strripos($this->path,"/");
	    $nameclean=substr($this->path,$nameint+1);
	    $pathclean=substr_replace($this->path,"",$nameint);
	    $this->disk->putFileAs($pathclean, $file_content, $nameclean, 'public');
    }

    public function url($path)
    {
        return $this->disk->url($path);
    }

    public function makeDirectory()
    {
        $this->disk->makeDirectory($this->path, ...func_get_args());

        $this->disk->setVisibility($this->path, 'public');
    }

    public function extension()
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }
}
