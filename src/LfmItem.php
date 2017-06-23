<?php

namespace Unisharp\Laravelfilemanager;

use UniSharp\LaravelFilemanager\Lfm;

class LfmItem
{
    protected $storage;
    protected $path;

    // TODO: thumb
    public function __construct(LfmStorage $storage, $path)
    {
        $this->storage = $storage;
        $this->path = $path;

        return;

        $this->initHelper();

        $path = new LfmPath;

        $file_name = $this->fileName();
        $full_path = $this->absolutePath();
        $is_file = ! $this->isDirectory();

        if (! $is_file) {
            $file_type = trans($this->package_name . '::lfm.type-folder');
            $icon = 'fa-folder-o';
            $thumb_url = asset('vendor/' . $this->package_name . '/img/folder.png');
        } elseif ($this->fileIsImage($storage_path)) {
            $file_type = $this->getFileType($storage_path);
            $icon = 'fa-image';

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
            $icon = config('lfm.file_icon_array.' . $extension) ?: 'fa-file';
            $thumb_url = null;
        }

        $this->name = $file_name;
        $this->url = $is_file ? $path->url($file_name) : '';
        $this->size = $is_file ? $this->humanFilesize($this->disk->size($storage_path)) : '';
        $this->updated = $this->disk->lastModified($storage_path);
        $this->path = $is_file ? '' : $path->path('full');
        $this->time = date('Y-m-d h:m', $this->disk->lastModified($storage_path));
        $this->type = $file_type;
        $this->icon = $icon;
        $this->thumb = $thumb_url;
        $this->is_file = $is_file;
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
        return $this->storage->disk->isDirectory($this->absolutePath());
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

        return $this->storage->disk->mimeType($this->absolutePath());
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
        return $this->storage->lfm->humanFilesize($this->storage->disk->size($this->absolutePath()));
    }

    // TODO: use carbon
    public function lastModified()
    {
        return $this->storage->disk->lastModified($this->absolutePath());
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
}
