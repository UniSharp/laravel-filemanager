<?php

namespace Unisharp\Laravelfilemanager;

use Illuminate\Support\Facades\Storage;

class LfmPath
{
    private $working_dir;
    private $item_name;
    private $is_thumb = false;

    public $helper;

    public function __construct(Lfm $lfm = null)
    {
        $this->helper = $lfm ?: new Lfm(config(), request());
    }

    public function __get($var_name)
    {
        if ($var_name == 'storage') {
            return new LfmStorage($this, Storage::disk($this->disk_name));
        } elseif ($var_name == 'disk_root') {
            return $this->helper->getDiskRoot();
        } elseif ($var_name == 'disk_name') {
            return 'local'; // config('lfm.disk')
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

    public function setName($item_name)
    {
        $this->item_name = $item_name;

        return $this;
    }

    public function getName()
    {
        return $this->item_name;
    }

    // /var/www/html/project/storage/app/laravel-filemanager/photos/{user_slug}
    // /var/www/html/project/storage/app/laravel-filemanager/photos/shares
    // absolute: /var/www/html/project/storage/app/laravel-filemanager/photos/shares
    // storage: laravel-filemanager/photos/shares
    // working directory: shares
    public function path ($type = 'storage')
    {
        $working_dir = $this->normalizeWorkingDir();

        // storage/app/laravel-filemanager/files/{user_slug}
        $full_path = $this->helper->basePath() . Lfm::DS . $this->getPathPrefix() . $working_dir;

        if ($type == 'storage') {
            // storage_path('app') /
            // laravel-filemanager/files/{user_slug}
            $result = str_replace($this->disk_root . Lfm::DS, '', $full_path);
        } elseif ($type == 'working_dir') {
            $result = $working_dir;
        } else {
            $result = $full_path;
        }

        if ($this->is_thumb) {
            $result .= Lfm::DS . config('lfm.thumb_folder_name');
        }

        if ($this->getName()) {
            $result .= Lfm::DS . $this->getName();
        }

        $this->reset();

        return $result;
    }

    public function url($with_timestamp = false)
    {
        $prefix = $this->helper->getUrlPrefix();

        $default_folder_name = 'files';
        if ($this->helper->isProcessingImages()) {
            $default_folder_name = 'photos';
        }

        $category_name = $this->helper->getCategoryName($this->currentLfmType());

        $working_dir = $this->normalizeWorkingDir();

        $result = $prefix . Lfm::DS . $category_name . $working_dir;

        if ($this->is_thumb) {
            $result .= Lfm::DS . config('lfm.thumb_folder_name');
        }

        if ($this->getName()) {
            $result .= Lfm::DS . $this->getName();
        }

        $this->reset();

        return $this->helper->url($result);
    }

    public function folders()
    {
        $all_folders = array_map(function ($directory_path) {
            return $this->get($directory_path);
        }, $this->storage->directories($this));

        $visible_folders = array_filter($all_folders, function ($directory) {
            return $directory->name !== $this->helper->getThumbFolderName();
        });

        $this->reset();

        return $visible_folders;
    }

    public function files()
    {
        $files = array_map(function ($file_path) {
            return $this->get($file_path);
        }, $this->storage->files($this));

        $this->reset();

        return $files;
    }

    public function get($item_path)
    {
        $item = new LfmItem($this->setName($this->helper->getNameFromPath($item_path)));

        $this->reset();

        return $item;
    }

    public function __call($function_name, $arguments)
    {
        $result = $this->storage->$function_name(...$arguments);

        $this->reset();

        return $result;
    }

    public function delete()
    {
        if ($this->isDirectory()) {
            return $this->storage->deleteDirectory();
        } else {
            return $this->storage->delete();
        }
    }

    /**
     * Create folder if not exist.
     *
     * @param  string  $path  Real path of a directory.
     * @return bool
     */
    public function createFolder()
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
        $category_name = $this->helper->getCategoryName($this->currentLfmType());

        // storage_path('app') / laravel-filemanager
        $prefix = $this->disk_root . Lfm::DS . Lfm::PACKAGE_NAME;

        // storage/app/laravel-filemanager
        $prefix = str_replace($this->helper->basePath() . Lfm::DS, '', $prefix);

        // storage/app/laravel-filemanager/files
        return $prefix . Lfm::DS . $category_name;
    }

    public function normalizeWorkingDir()
    {
        $working_dir = $this->working_dir ?: $this->helper->input('working_dir');

        if (empty($working_dir)) {
            $default_folder_type = 'share';
            if ($this->helper->allowFolderType('user')) {
                $default_folder_type = 'user';
            }

            $working_dir = $this->helper->getRootFolder($default_folder_type);
        }

        return $working_dir;
    }

    /**
     * Get current lfm type..
     *
     * @return string
     */
    private function currentLfmType()
    {
        $file_type = 'file';
        if ($this->helper->isProcessingImages()) {
            $file_type = 'image';
        }

        return $file_type;
    }

    // TODO: maybe is useless
    private function reset()
    {
        // $this->working_dir = null;
        // $this->item_name = null;
        // $this->is_thumb = false;
    }
}
