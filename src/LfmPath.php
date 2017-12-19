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

    public function path($type = 'storage')
    {
        if ($type == 'working_dir') {
            // working directory: /{user_slug}
            $result = $this->normalizeWorkingDir();
            return $this->appendPathToFile($result, false);
        } elseif ($type == 'storage') {
            // storage: files/{user_slug}
            $result = $this->helper->getCategoryName() . $this->normalizeWorkingDir();
            return $this->appendPathToFile($result, $this->helper->isRunningOnWindows());
        } else {
            // absolute: /var/www/html/project/storage/app/files/{user_slug}
            $result = $this->storage->rootPath() . $this->helper->getCategoryName() . $this->normalizeWorkingDir();
            return $this->appendPathToFile($result, $this->helper->isRunningOnWindows());
        }
    }

    // TODO: work with timestamp
    public function url($with_timestamp = false)
    {
        return Lfm::DS . $this->path('storage');
    }

    public function appendPathToFile($path, $is_windows = false)
    {
        $ds = Lfm::DS;
        if ($is_windows) {
            $ds = '\\';
        }

        if ($this->is_thumb) {
            $path .= $ds . $this->helper->getThumbFolderName();
        }

        if ($this->getName()) {
            $path .= $ds . $this->getName();
        }

        return $path;
    }

    public function folders()
    {
        $all_folders = array_map(function ($directory_path) {
            return $this->get($directory_path);
        }, $this->storage->directories($this));

        $folders = array_filter($all_folders, function ($directory) {
            return $directory->name !== $this->helper->getThumbFolderName();
        });

        return $this->sortByColumn($folders);
    }

    public function files()
    {
        $files = array_map(function ($file_path) {
            return $this->get($file_path);
        }, $this->storage->files());

        return $this->sortByColumn($files);
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
        return $this->working_dir
            ?: $this->helper->input('working_dir')
            ?: $this->helper->getRootFolder();
    }

    /**
     * Sort files and directories.
     *
     * @param  mixed  $arr_items  Array of files or folders or both.
     * @param  mixed  $sort_type  Alphabetic or time.
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
        $new_filename = $this->getNewName($file);
        $new_file_path = $this->setName($new_filename)->path('absolute');

        event(new ImageIsUploading($new_file_path));
        try {
            $new_filename = $this->saveFile($file, $new_filename);
        } catch (\Exception $e) {
            \Log::info($e);
            return $this->error('invalid');
        }
        // TODO should be "FileWasUploaded"
        event(new ImageWasUploaded($new_file_path));

        return $new_filename;
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

        $new_filename = $this->getNewName($file) . '.' . $file->getClientOriginalExtension();

        if ($this->setName($new_filename)->exists()) {
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
        $new_filename = $this->helper->translateFromUtf8(trim(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)));

        if (config('lfm.rename_file') === true) {
            $new_filename = uniqid();
        } elseif (config('lfm.alphanumeric_filename') === true) {
            $new_filename = preg_replace('/[^A-Za-z0-9\-\']/', '_', $new_filename);
        }

        $extension = $file->getClientOriginalExtension();

        if ($extension) {
            $new_filename .= '.' . $extension;
        }

        return $new_filename;
    }

    private function saveFile($file, $new_filename)
    {
        $should_create_thumbnail = $this->shouldCreateThumb($file);

        $this->setName(null)->thumb(false)->storage->save($file, $new_filename);

        chmod($this->setName($new_filename)->thumb(false)->path('absolute'), config('lfm.create_file_mode', 0644));

        if ($should_create_thumbnail) {
            $this->makeThumbnail($new_filename);
        }

        return $new_filename;
    }

    public function makeThumbnail($filename)
    {
        // create folder for thumbnails
        $this->setName(null)->thumb(true)->createFolder();

        // generate cropped thumbnail
        Image::make($this->thumb(false)->setName($filename)->path('absolute'))
            ->fit(config('lfm.thumb_img_width', 200), config('lfm.thumb_img_height', 200))
            ->save($this->thumb(true)->setName($filename)->path('absolute'));
    }

    private function shouldCreateThumb($file)
    {
        return starts_with($file->getMimeType(), 'image')
            && !in_array($file->getMimeType(), ['image/gif', 'image/svg+xml']);
    }
}
