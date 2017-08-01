<?php

namespace UniSharp\LaravelFilemanager;

use Illuminate\Container\Container;

class LfmPath
{
    private $working_dir;
    private $item_name;
    private $is_thumb = false;

    private $helper;

    public function __construct(Lfm $lfm = null)
    {
        $this->helper = $lfm;
    }

    public function __get($var_name)
    {
        if ($var_name == 'storage') {
            return $this->helper->getStorage($this->path('storage'));
        }
    }

    public function __call($function_name, $arguments)
    {
        $result = $this->storage->$function_name(...$arguments);

        $this->reset();

        return $result;
    }

    public function dir($working_dir)
    {
        $this->working_dir = $working_dir;

        return $this;
    }

    public function thumb($is_thumb = true)
    {
        $this->is_thumb = $is_thumb;

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
        $prefix = Lfm::PACKAGE_NAME;

        $storage_path = $this->appendStorageFullPath($prefix);

        if ($type == 'storage') {
            // laravel-filemanager/files/{user_slug}
            $result = $storage_path;
        } elseif ($type == 'working_dir') {
            // {user_slug}
            $result = $this->normalizeWorkingDir();
        } else {
            // /var/www/html/project/storage/app/laravel-filemanager/files/{user_slug}
            $result = $this->storage->rootPath() . $storage_path;
        }

        $result = $this->appendPathToFile($result);

        $this->reset();

        return $result;
    }

    public function url($with_timestamp = false)
    {
        $prefix = $this->helper->getUrlPrefix();

        $result = $this->appendStorageFullPath($prefix);

        $result = $this->appendPathToFile($result);

        $this->reset();

        return $this->helper->url($result);
    }

    public function appendStorageFullPath($path)
    {
        return $path                                      // laravel-filemanager
            . Lfm::DS . $this->helper->getCategoryName()  // files
            . $this->normalizeWorkingDir();               // {user_slug}
    }

    public function appendPathToFile($path)
    {
        if ($this->is_thumb) {
            $path .= Lfm::DS . $this->helper->getThumbFolderName();
        }

        if ($this->getName()) {
            $path .= Lfm::DS . $this->getName();
        }

        return $path;
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
        $lfm_path = $this->setName($this->helper->getNameFromPath($item_path));

        $item = app(LfmItem::class, [$lfm_path, $this->helper]);

        $this->reset();

        return $item;
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

    // TODO: maybe is useless
    private function reset()
    {
        // $this->working_dir = null;
        // $this->item_name = null;
        // $this->is_thumb = false;
    }
}
