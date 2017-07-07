<?php

namespace Unisharp\Laravelfilemanager;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LfmPath
{
    // TODO: remove it
    private $ds = '/';

    // TODO: remove it
    protected $package_name = 'laravel-filemanager';

    private $working_dir;
    private $full_path;
    private $is_thumb = false;
    public $lfm;
    private $disk_name = 'local'; // config('lfm.disk')

    public $request;

    public function __construct(Lfm $lfm = null, Request $request = null)
    {
        $this->lfm = $lfm ?: new Lfm(config());
        $this->request = $request;
    }

    public function __get($var_name)
    {
        if ($var_name == 'storage') {
            return new LfmStorage($this, Storage::disk($this->disk_name));
        } elseif ($var_name == 'disk_root') {
            return $this->lfm->getDiskRoot();
        }
    }

    public function dir($working_dir)
    {
        $this->working_dir = $working_dir;

        return $this;
    }

    public function thumb()
    {
        $this->is_thumb = true;

        return $this;
    }

    // /var/www/html/project/storage/app/laravel-filemanager/photos/{user_slug}
    // /var/www/html/project/storage/app/laravel-filemanager/photos/shares
    // absolute: /var/www/html/project/storage/app/laravel-filemanager/photos/shares
    // storage: laravel-filemanager/photos/shares
    // working directory: shares
    public function path($type = 'storage', $item_name = null)
    {
        $this->working_dir = $this->normalizeWorkingDir();

        // storage/app/laravel-filemanager/files/{user_slug}
        $this->full_path = $this->lfm->basePath() . $this->ds . $this->getPathPrefix() . $this->working_dir;

        if ($type == 'storage') {
            // storage_path('app') /
            // laravel-filemanager/files/{user_slug}
            $result = str_replace($this->disk_root . $this->ds, '', $this->full_path);
        } elseif ($type == 'working_dir') {
            $result = $this->working_dir;
        } else {
            $result = $this->full_path;
        }

        if ($this->is_thumb) {
            $result .= $this->ds . config('lfm.thumb_folder_name');
        }

        if ($item_name) {
            $result .= $this->ds . $item_name;
        }

        return $result;
    }

    public function url($item_name, $with_timestamp = false)
    {
        $prefix = $this->lfm->getUrlPrefix();

        // dd($this->request->input());

        $default_folder_name = 'files';
        if ($this->isProcessingImages()) {
            $default_folder_name = 'photos';
        }

        $category_name = $this->lfm->getCategoryName($this->currentLfmType());

        $this->working_dir = $this->normalizeWorkingDir();

        $result = $prefix . $this->ds . $category_name . $this->working_dir;

        if ($this->is_thumb) {
            $result .= $this->ds . config('lfm.thumb_folder_name');
        }

        if ($item_name) {
            $result .= $this->ds . $item_name;
        }

        return $this->lfm->url($result);
    }

    public function folders()
    {
        $folders = array_map(function ($directory) {
            return $this->get($directory);
        }, $this->storage->directories($this));

        return array_filter($folders, function ($directory) {
            return $directory->name !== $this->lfm->getThumbFolderName();
        });
    }

    public function files()
    {
        return array_map(function ($file_name) {
            return $this->get($file_name);
        }, $this->storage->files($this));
    }

    public function exists($item_name)
    {
        return $this->storage->exists($this);
    }

    public function get($item_name)
    {
        return new LfmItem($this);
    }

    public function __call($function_name, $arguments)
    {
        return $this->storage->$function_name();
    }

    /**
     * Create folder if not exist.
     *
     * @param  string  $path  Real path of a directory.
     * @return bool
     */
    public function createFolder($item_name)
    {
        if ($this->storage->exists($this)) {
            return false;
        }

        return $this->storage->makeDirectory($this);
    }

    /**
     * Assemble base_directory and route prefix config.
     *
     * @param  string  $type  Url or dir
     * @return string
     */
    public function getPathPrefix()
    {
        $category_name = $this->lfm->getCategoryName($this->currentLfmType());

        // storage_path('app') / laravel-filemanager
        $prefix = $this->disk_root . $this->ds . $this->package_name;

        // storage/app/laravel-filemanager
        $prefix = str_replace($this->lfm->basePath() . $this->ds, '', $prefix);

        // storage/app/laravel-filemanager/files
        return $prefix . $this->ds . $category_name;
    }

    /**
     * Check current lfm type is image or not.
     *
     * @return bool
     */
    public function isProcessingImages()
    {
        return lcfirst(str_singular($this->request->input('type'))) === 'image';
    }

    public function normalizeWorkingDir()
    {
        $this->working_dir = $this->working_dir ?: $this->request->input('working_dir');

        if (empty($this->working_dir)) {
            $default_folder_type = 'share';
            if ($this->lfm->allowFolderType('user')) {
                $default_folder_type = 'user';
            }

            $this->working_dir = $this->lfm->getRootFolder($default_folder_type);
        }

        return $this->working_dir;
    }

    /**
     * Get current lfm type..
     *
     * @return string
     */
    private function currentLfmType()
    {
        $file_type = 'file';
        if ($this->isProcessingImages()) {
            $file_type = 'image';
        }

        return $file_type;
    }

    // TODO: maybe is useless
    private function reset()
    {
        $this->working_dir = null;
        $this->full_path = null;
    }
}
