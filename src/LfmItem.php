<?php

namespace serwin35\LaravelFilemanager;

use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LfmItem
{
    private $lfm;
    private $helper;

    private $columns = ['name', 'url', 'time', 'icon', 'is_file', 'is_image', 'thumb_url'];
    public $attributes = [];

    public function __construct(LfmPath $lfm, Lfm $helper)
    {
        $this->lfm = $lfm->thumb(false);
        $this->helper = $helper;
    }

    public function __get($var_name)
    {
        if (!array_key_exists($var_name, $this->attributes)) {
            $function_name = Str::camel($var_name);
            $this->attributes[$var_name] = $this->$function_name();
        }

        return $this->attributes[$var_name];
    }

    public function fill()
    {
        foreach ($this->columns as $column) {
            $this->__get($column);
        }

        return $this;
    }

    public function name()
    {
        return $this->lfm->getName();
    }

    public function path($type = 'absolute')
    {
        return $this->lfm->path($type);
    }

    public function isDirectory()
    {
        return $this->lfm->isDirectory();
    }

    public function isFile()
    {
        return ! $this->isDirectory();
    }

    /**
     * Check a file is image or not.
     *
     * @param  mixed  $file  Real path of a file or instance of UploadedFile.
     * @return bool
     */
    public function isImage()
    {
        if (!$this->isDirectory()) {
            return Str::startsWith($this->mimeType(), 'image');
        }

        return false;
    }

    /**
     * Get mime type of a file.
     *
     * @param  mixed  $file  Real path of a file or instance of UploadedFile.
     * @return string
     */
    // TODO: uploaded file
    public function mimeType()
    {
        // if ($file instanceof UploadedFile) {
        //     return $file->getMimeType();
        // }

        return $this->lfm->mimeType();
    }

    public function extension()
    {
        return $this->lfm->extension();
    }

    public function url()
    {
        if ($this->isDirectory()) {
            return $this->lfm->path('working_dir');
        }

        return $this->lfm->url();
    }

    public function size()
    {
        return $this->isFile() ? $this->humanFilesize($this->lfm->size()) : '';
    }

    public function time()
    {
        if (!$this->isDirectory()) {
            return $this->lfm->lastModified();
        }

        return false;
    }

    public function thumbUrl()
    {
        if ($this->isDirectory()) {
            return asset('vendor/' . Lfm::PACKAGE_NAME . '/img/folder.png');
        }

        if ($this->isImage()) {
            return $this->lfm->thumb($this->hasThumb())->url(true);
        }

        return null;
    }

    public function icon()
    {
        if ($this->isDirectory()) {
            return 'fa-folder-o';
        }

        if ($this->isImage()) {
            return 'fa-image';
        }

        return $this->extension();
    }

    public function type()
    {
        if ($this->isDirectory()) {
            return trans(Lfm::PACKAGE_NAME . '::lfm.type-folder');
        }

        if ($this->isImage()) {
            return $this->mimeType();
        }

        return $this->helper->getFileType($this->extension());
    }

    public function hasThumb()
    {
        if (!$this->isImage()) {
            return false;
        }

        if (!$this->lfm->thumb()->exists()) {
            return false;
        }

        return true;
    }

    public function shouldCreateThumb()
    {
        if (!$this->helper->config('should_create_thumbnails')) {
            return false;
        }

        if (!$this->isImage()) {
            return false;
        }

        if (in_array($this->mimeType(), ['image/gif', 'image/svg+xml'])) {
            return false;
        }

        return true;
    }

    public function get()
    {
        return $this->lfm->get();
    }

    /**
     * Make file size readable.
     *
     * @param  int  $bytes     File size in bytes.
     * @param  int  $decimals  Decimals.
     * @return string
     */
    public function humanFilesize($bytes, $decimals = 2)
    {
        $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f %s", $bytes / pow(1024, $factor), @$size[$factor]);
    }
}
