<?php

namespace UniSharp\LaravelFilemanager\Controllers;

use Illuminate\Support\Facades\File;
use UniSharp\LaravelFilemanager\Events\ImageIsRenaming;
use UniSharp\LaravelFilemanager\Events\ImageWasRenamed;
use UniSharp\LaravelFilemanager\Events\FolderIsRenaming;
use UniSharp\LaravelFilemanager\Events\FolderWasRenamed;

/**
 * Class RenameController.
 */
class RenameController extends LfmController
{
    /**
     * @return string
     */
    public function getRename()
    {
        $old_name = parent::translateFromUtf8(request('file'));
        $new_name = parent::translateFromUtf8(trim(request('new_name')));

        $old_file = parent::getCurrentPath($old_name);
        if (File::isDirectory($old_file)) {
            return $this->renameDirectory($old_name, $new_name);
        } else {
            return $this->renameFile($old_name, $new_name);
        }
    }

    protected function renameDirectory($old_name, $new_name)
    {
        if (empty($new_name)) {
            return parent::error('folder-name');
        }

        $old_file = parent::getCurrentPath($old_name);
        $new_file = parent::getCurrentPath($new_name);

        event(new FolderIsRenaming($old_file, $new_file));

        if (config('lfm.alphanumeric_directory') && preg_match('/[^\w-]/i', $new_name)) {
            return parent::error('folder-alnum');
        }

        if (File::exists($new_file)) {
            return parent::error('rename');
        }

        File::move($old_file, $new_file);
        event(new FolderWasRenamed($old_file, $new_file));

        return parent::$success_response;
    }

    protected function renameFile($old_name, $new_name)
    {
        if (empty($new_name)) {
            return parent::error('file-name');
        }

        $old_file = parent::getCurrentPath($old_name);
        $extension = File::extension($old_file);
        $new_file = parent::getCurrentPath(basename($new_name, ".$extension") . ".$extension");

        if (config('lfm.alphanumeric_filename') && preg_match('/[^\w-.]/i', $new_name)) {
            return parent::error('file-alnum');
        }

        // TODO Should be "FileIsRenaming"
        event(new ImageIsRenaming($old_file, $new_file));

        if (File::exists($new_file)) {
            return parent::error('rename');
        }

        if (parent::fileIsImage($old_file) && File::exists(parent::getThumbPath($old_name))) {
            File::move(parent::getThumbPath($old_name), parent::getThumbPath($new_name));
        }

        File::move($old_file, $new_file);
        // TODO Should be "FileWasRenamed"
        event(new ImageWasRenamed($old_file, $new_file));

        return parent::$success_response;
    }
}
