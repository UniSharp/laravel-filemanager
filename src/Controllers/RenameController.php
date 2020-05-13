<?php

namespace UniSharp\LaravelFilemanager\Controllers;

use Illuminate\Support\Str;
use UniSharp\LaravelFilemanager\Events\ImageIsRenaming;
use UniSharp\LaravelFilemanager\Events\ImageWasRenamed;
use UniSharp\LaravelFilemanager\Events\FolderIsRenaming;
use UniSharp\LaravelFilemanager\Events\FolderWasRenamed;

class RenameController extends LfmController
{
    public function getRename()
    {
        $old_name = $this->helper->input('file');
        $new_name = $this->helper->input('new_name');

        $old_file = $this->lfm->pretty($old_name);

        $is_directory = $old_file->isDirectory();

        if (empty($new_name)) {
            if ($is_directory) {
                return parent::error('folder-name');
            } else {
                return parent::error('file-name');
            }
        }

        if ($is_directory && config('lfm.alphanumeric_directory')) {
            if (config('lfm.convert_to_alphanumeric')) {
                $new_name = Str::slug($new_name);
            }

            if (preg_match('/[^\w\-_]/i', $new_name)) {
                return parent::error('folder-alnum');
            }
        }else if(!$is_directory && config('lfm.alphanumeric_filename')){
            // Remove extension for checks to alphanum characters
            $extension = $old_file->extension();
            if ($extension) {
                $new_name = str_replace('.' . $extension, '', $new_name);
            }

            if (config('lfm.convert_to_alphanumeric')) {
                $new_name = Str::slug($new_name);
            }

            if (preg_match('/[^\w\-_]/i', $new_name)) {
                return parent::error('file-alnum');
            }

            $new_name .= ($extension) ? '.' . $extension : null;
        }

        if ($this->lfm->setName($new_name)->exists()) {
            return parent::error('rename');
        }

        $new_file = $this->lfm->setName($new_name)->path('absolute');

        if ($is_directory) {
            event(new FolderIsRenaming($old_file->path(), $new_file));
        } else {
            event(new ImageIsRenaming($old_file->path(), $new_file));
        }

        if ($old_file->hasThumb()) {
            $this->lfm->setName($old_name)->thumb()
                ->move($this->lfm->setName($new_name)->thumb());
        }

        $this->lfm->setName($old_name)
            ->move($this->lfm->setName($new_name));

        if ($is_directory) {
            event(new FolderWasRenamed($old_file->path(), $new_file));
        } else {
            event(new ImageWasRenamed($old_file->path(), $new_file));
        }

        return parent::$success_response;
    }
}
