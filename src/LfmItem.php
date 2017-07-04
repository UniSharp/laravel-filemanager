<?php

namespace Unisharp\Laravelfilemanager;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use UniSharp\LaravelFilemanager\Lfm;
use Illuminate\Http\Request;

class LfmItem
{
    public $storage;
    public $path;
    private $lfm;
    protected $attributes = [];

    // TODO: thumb
    public function __construct(LfmStorage $storage, $path)
    {
        $this->storage = $storage;
        $this->path = $path;
    }

    public function __get($var_name)
    {
        $this->lfm = new Lfm(config());
        $path = new LfmPath($this->lfm, new Request);

        $file_name = $this->fileName();
        $full_path = $this->absolutePath();
        $is_file = ! $this->isDirectory();

        if (! $is_file) {
            $file_type = trans(Lfm::PACKAGE_NAME . '::lfm.type-folder');
            $thumb_url = asset('vendor/' . Lfm::PACKAGE_NAME . '/img/folder.png');
        } elseif ($this->isImage()) {
            $file_type = $this->mimeType();

            if ($this->imageShouldNotHaveThumb()) {
                $thumb_url = $path->url($file_name, true);
            } elseif ($path->thumb()->exists($file_name)) {
                $thumb_url = $path->thumb()->url($file_name, true);
            } else {
                $thumb_url = $path->url($file_name, true);
            }
        } else {
            $extension = strtolower(\File::extension($file_name));
            $file_type = $this->lfm->getFileType($extension);
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

        if (array_key_exists($var_name, $this->attributes)) {
            return $this->attributes[$var_name];
        }
    }

    public function fileName()
    {
        $segments = explode('/', $this->path);
        return end($segments);
    }

    public function absolutePath()
    {
        return $this->storage->disk_root . Lfm::DS . $this->path;
    }

    public function isDirectory()
    {
        return $this->storage->isDirectory($this->path);
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

        return $this->storage->mimeType($this->path);
    }

    public function extension()
    {
        return pathinfo($this->absolutePath(), PATHINFO_EXTENSION);
        // return $this->storage->disk->extension($this->absolutePath());
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
        return $this->storage->disk->lastModified($this->path);
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
