<?php

namespace Unisharp\Laravelfilemanager;

class LfmItem
{
    public $name;
    public $url;
    public $size;
    public $updated;
    public $path;
    public $time;
    public $type;
    public $icon;
    public $thumb;
    public $is_file;

    use traits\LfmHelpers;

    public function __construct($storage_path)
    {
        $this->initHelper();

        $file_name = $this->getName($storage_path);
        $full_path = $this->getFullPath($storage_path);
        $is_file = !$this->isDirectory($full_path);

        if (!$is_file) {
            $file_type = trans($this->package_name . '::lfm.type-folder');
            $icon = 'fa-folder-o';
            $thumb_url = asset('vendor/' . $this->package_name . '/img/folder.png');
        } elseif ($this->fileIsImage($storage_path)) {
            $file_type = $this->getFileType($storage_path);
            $icon = 'fa-image';

            $thumb_path = $this->getThumbPath($file_name);
            $file_path = $this->getCurrentPath($file_name);
            if ($this->imageShouldNotHaveThumb($file_path)) {
                $thumb_url = $this->getFileUrl($file_name, true);
            } elseif ($this->exists($thumb_path)) {
                $thumb_url = $this->getThumbUrl($file_name, true);
            } else {
                $thumb_url = $this->getFileUrl($file_name, true);
            }
        } else {
            $extension = strtolower(\File::extension($file_name));
            $file_type = config('lfm.file_type_array.' . $extension) ?: 'File';
            $icon = config('lfm.file_icon_array.' . $extension) ?: 'fa-file';
            $thumb_url = null;
        }

        $this->name    = $file_name;
        $this->url     = $is_file ? $this->getFileUrl($file_name) : '';
        $this->size    = $is_file ? $this->humanFilesize($this->disk->size($storage_path)) : '';
        $this->updated = $this->disk->lastModified($storage_path);
        $this->path    = $is_file ? '' : $this->getInternalPath($full_path);
        $this->time    = date("Y-m-d h:m", $this->disk->lastModified($storage_path));
        $this->type    = $file_type;
        $this->icon    = $icon;
        $this->thumb   = $thumb_url;
        $this->is_file = $is_file;
    }
}
