<?php

namespace Unisharp\Laravelfilemanager;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use UniSharp\LaravelFilemanager\Lfm;

class LfmItem
{
    protected $storage;
    protected $path;
    protected $attributes = [];

    // TODO: thumb
    public function __construct(LfmStorage $storage, $path)
    {
        $this->storage = $storage;
        $this->path = $path;

        // return;

        $lfm = new Lfm(config());
        $lfm->setStorage(new LfmStorage());
        $path = new LfmPath($lfm, request());

        $file_name = $this->fileName();
        $full_path = $this->absolutePath();
        $is_file = ! $this->isDirectory();

        if (! $is_file) {
            $file_type = trans($this->package_name . '::lfm.type-folder');
            $thumb_url = asset('vendor/' . $this->package_name . '/img/folder.png');
        } elseif ($this->isImage()) {
            $file_type = $this->mimeType();

            $file_path = $path->path('full', $file_name);
            if ($this->imageShouldNotHaveThumb($file_path)) {
                $thumb_url = $path->url($file_name, true);
            } elseif ($path->thumb()->exists($file_name)) {
                $thumb_url = $path->thumb()->url($file_name, true);
            } else {
                $thumb_url = $path->url($file_name, true);
            }
        } else {
            $extension = strtolower(\File::extension($file_name));
            $file_type = config('lfm.file_type_array.' . $extension) ?: 'File';
            $thumb_url = null;
        }

        $this->attributes['name']    = $file_name;
        $this->attributes['url']     = $is_file ? $path->url($file_name) : '';
        $this->attributes['size']    = $is_file ? $this->size() : '';
        $this->attributes['updated'] = $this->lastModified();
        $this->attributes['path']    = $is_file ? '' : $this->absolutePath();
        $this->attributes['time']    = date('Y-m-d h:m', $this->lastModified());
        $this->attributes['type']    = $file_type;
        $this->attributes['icon']    = $this->icon();
        $this->attributes['thumb']   = $thumb_url;
        $this->attributes['is_file'] = $is_file;
    }

    public function __get($var_name)
    {
        if (array_key_exists($var_name, $this->attributes)) {
            return $this->attributes[$var_name];
        }
    }

    public function fileName()
    {
        return basename($this->path);
    }

    public function absolutePath()
    {
        return $this->storage->disk_root . Lfm::DS . $this->path;
    }

    public function isDirectory()
    {
        return is_dir($this->absolutePath());
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
        return starts_with($this->mimeType(), 'image');
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

        return $this->storage->disk->mimeType($this->path);
    }

    public function extension()
    {
        return $this->storage->disk->extension($this->absolutePath());
    }

    // TODO: check directory
    public function url()
    {
        return $this->storage->lfm->url($this->absolutePath());
    }

    // TODO: check directory
    public function size()
    {
        return $this->humanFilesize($this->storage->disk->size($this->path));
    }

    // TODO: use carbon
    public function lastModified()
    {
        return filemtime($this->absolutePath());
    }

    public function icon()
    {
        if ($this->isDirectory()) {
            return 'fa-folder-o';
        }

        if ($this->isImage()) {
            return 'fa-image';
        }

        return $this->storage->lfm->getFileIcon($this->extension());
    }

    public function imageShouldNotHaveThumb()
    {
        $mine_type = $this->mimeType();
        $noThumbType = ['image/gif', 'image/svg+xml'];

        return in_array($mine_type, $noThumbType);
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
