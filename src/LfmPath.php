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
        return $this->storage->$function_name(...$arguments);
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

    public function path ($type = 'storage')
    {
        if ($type == 'working_dir') {
            // working directory: /{user_slug}
            $result = $this->normalizeWorkingDir();
        } elseif ($type == 'storage') {
            // storage: files/{user_slug}
            $result = $this->helper->getCategoryName() . $this->normalizeWorkingDir();
        } else {
            // absolute: /var/www/html/project/storage/app/files/{user_slug}
            $result = $this->storage->rootPath() . $this->helper->getCategoryName() . $this->normalizeWorkingDir();
        }

        return $this->appendPathToFile($result);
    }

    public function url($with_timestamp = false)
    {
        return Lfm::DS . $this->path('storage');
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

    public function folders($column = null)
    {
        $all_folders = array_map(function ($directory_path) {
            return $this->get($directory_path);
        }, $this->storage->directories($this));

        $folders = array_filter($all_folders, function ($directory) {
            return $directory->name !== $this->helper->getThumbFolderName();
        });

        return $this->sortByColumn($folders, $column);
    }

    public function files($column = null)
    {
        $files = array_map(function ($file_path) {
            return $this->get($file_path);
        }, $this->storage->files());

        return $this->sortByColumn($files, $column);
    }

    public function get($item_path)
    {
        $lfm_path = clone $this;
        $lfm_path = $lfm_path->setName($this->helper->getNameFromPath($item_path));

        return Container::getInstance()->make(LfmItem::class, [$lfm_path, $this->helper]);
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

    /**
     * Sort files and directories.
     *
     * @param  mixed  $arr_items  Array of files or folders or both.
     * @param  mixed  $sort_type  Alphabetic or time.
     * @return array of object
     */
    public function sortByColumn($arr_items, $key_to_sort = null)
    {
        if (is_null($key_to_sort)) {
            $sort_type = $this->helper->input('sort_type');
            if (!$sort_type || $sort_type == 'alphabetic') {
                $key_to_sort = 'name';
            } else {
                $key_to_sort = 'time';
            }
        }

        uasort($arr_items, function ($a, $b) use ($key_to_sort) {
            return strcmp($a->{$key_to_sort}, $b->{$key_to_sort});
        });

        return $arr_items;
    }
}
