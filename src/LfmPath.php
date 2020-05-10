<?php

namespace UniSharp\LaravelFilemanager;

use Illuminate\Container\Container;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use UniSharp\LaravelFilemanager\Events\ImageIsUploading;
use UniSharp\LaravelFilemanager\Events\ImageWasUploaded;

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
            return $this->helper->getStorage($this->path('url'));
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

    public function path($type = 'storage')
    {
        if ($type == 'working_dir') {
            // working directory: /{user_slug}
            return $this->translateToLfmPath($this->normalizeWorkingDir());
        } elseif ($type == 'url') {
            // storage: files/{user_slug}
            return $this->helper->getCategoryName() . $this->path('working_dir');
        } elseif ($type == 'storage') {
            // storage: files/{user_slug}
            // storage on windows: files\{user_slug}
            return str_replace(Lfm::DS, $this->helper->ds(), $this->path('url'));
        } else {
            // absolute: /var/www/html/project/storage/app/files/{user_slug}
            // absolute on windows: C:\project\storage\app\files\{user_slug}
            return $this->storage->rootPath() . $this->path('storage');
        }
    }

    public function translateToLfmPath($path)
    {
        return str_replace($this->helper->ds(), Lfm::DS, $path);
    }

    public function url()
    {
        return $this->storage->url($this->path('url'));
    }

    public function folders()
    {
        $all_folders = array_map(function ($directory_path) {
            return $this->pretty($directory_path, true);
        }, $this->storage->directories());

        $folders = array_filter($all_folders, function ($directory) {
            return $directory->name !== $this->helper->getThumbFolderName();
        });

        return $this->sortByColumn($folders);
    }

    public function files()
    {
        $files = array_map(function ($file_path) {
            return $this->pretty($file_path);
        }, $this->storage->files());

        return $this->sortByColumn($files);
    }

    public function pretty($item_path, $isDirectory = false)
    {
        return Container::getInstance()->makeWith(LfmItem::class, [
            'lfm' => (clone $this)->setName($this->helper->getNameFromPath($item_path)),
            'helper' => $this->helper,
            'isDirectory' => $isDirectory
        ]);
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

        $this->storage->makeDirectory(0777, true, true);
    }

    public function isDirectory()
    {
        $working_dir = $this->path('working_dir');
        $parent_dir = substr($working_dir, 0, strrpos($working_dir, '/'));

        $parent_directories = array_map(function ($directory_path) {
            return app(static::class)->translateToLfmPath($directory_path);
        }, app(static::class)->dir($parent_dir)->directories());

        return in_array($this->path('url'), $parent_directories);
    }

    /**
     * Check a folder and its subfolders is empty or not.
     *
     * @param  string  $directory_path  Real path of a directory.
     * @return bool
     */
    public function directoryIsEmpty()
    {
        return count($this->storage->allFiles()) == 0;
    }

    public function normalizeWorkingDir()
    {
        $path = $this->working_dir
            ?: $this->helper->input('working_dir')
            ?: $this->helper->getRootFolder();

        if ($this->is_thumb) {
            // Prevent if working dir is "/" normalizeWorkingDir will add double "//" that breaks S3 functionality
            $path = rtrim($path, Lfm::DS) . Lfm::DS . $this->helper->getThumbFolderName();
        }

        if ($this->getName()) {
            // Prevent if working dir is "/" normalizeWorkingDir will add double "//" that breaks S3 functionality
            $path = rtrim($path, Lfm::DS) . Lfm::DS . $this->getName();
        }

        return $path;
    }

    /**
     * Sort files and directories.
     *
     * @param  mixed  $arr_items  Array of files or folders or both.
     * @return array of object
     */
    public function sortByColumn($arr_items)
    {
        $sort_by = $this->helper->input('sort_type');
        if (in_array($sort_by, ['name', 'time'])) {
            $key_to_sort = $sort_by;
        } else {
            $key_to_sort = 'name';
        }

        uasort($arr_items, function ($a, $b) use ($key_to_sort) {
            return strcmp($a->{$key_to_sort}, $b->{$key_to_sort});
        });

        return $arr_items;
    }

    public function error($error_type, $variables = [])
    {
        return $this->helper->error($error_type, $variables);
    }

    // Upload section
    public function upload($file)
    {
        $this->uploadValidator($file);
        $new_file_name = $this->getNewName($file);
        $new_file_path = $this->setName($new_file_name)->path('absolute');

        event(new ImageIsUploading($new_file_path));
        try {
            $new_file_name = $this->saveFile($file, $new_file_name);
        } catch (\Exception $e) {
            \Log::info($e);
            return $this->error('invalid');
        }
        // TODO should be "FileWasUploaded"
        event(new ImageWasUploaded($new_file_path));

        return $new_file_name;
    }

    private function uploadValidator($file)
    {
        if (empty($file)) {
            return $this->error('file-empty');
        } elseif (! $file instanceof UploadedFile) {
            return $this->error('instance');
        } elseif ($file->getError() == UPLOAD_ERR_INI_SIZE) {
            return $this->error('file-size', ['max' => ini_get('upload_max_filesize')]);
        } elseif ($file->getError() != UPLOAD_ERR_OK) {
            throw new \Exception('File failed to upload. Error code: ' . $file->getError());
        }

        $new_file_name = $this->getNewName($file);

        if ($this->setName($new_file_name)->exists() && !config('lfm.over_write_on_duplicate')) {
            return $this->error('file-exist');
        }

        if (config('lfm.should_validate_mime', false)) {
            $mimetype = $file->getMimeType();
            if (false === in_array($mimetype, $this->helper->availableMimeTypes())) {
                return $this->error('mime') . $mimetype;
            }
        }

        if (config('lfm.should_validate_size', false)) {
            // size to kb unit is needed
            $file_size = $file->getSize() / 1000;
            if ($file_size > $this->helper->maxUploadSize()) {
                return $this->error('size') . $file_size;
            }
        }

        return 'pass';
    }

    private function getNewName($file)
    {
        $new_file_name = $this->helper
            ->translateFromUtf8(trim(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)));

        if (config('lfm.rename_file') === true) {
            $new_file_name = uniqid();
        } elseif (config('lfm.alphanumeric_filename') === true) {
            $new_file_name = preg_replace('/[^A-Za-z0-9\-\']/', '_', $new_file_name);
        }

        $extension = $file->getClientOriginalExtension();

        if ($extension) {
            $new_file_name .= '.' . $extension;
        }

        return $new_file_name;
    }

    private function saveFile($file, $new_file_name)
    {
        $this->setName($new_file_name)->storage->save($file);

        $this->makeThumbnail($new_file_name);

        return $new_file_name;
    }

    public function makeThumbnail($file_name)
    {
        $original_image = $this->pretty($file_name);

        if (!$original_image->shouldCreateThumb()) {
            return;
        }

        // create folder for thumbnails
        $this->setName(null)->thumb(true)->createFolder();

        // generate cropped image content
        $this->setName($file_name)->thumb(true);
        $image = Image::make($original_image->get())
            ->fit(config('lfm.thumb_img_width', 200), config('lfm.thumb_img_height', 200));

        $this->storage->put($image->stream()->detach(), 'public');
    }
}
