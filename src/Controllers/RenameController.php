<?php

namespace UniSharp\LaravelFilemanager\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use UniSharp\LaravelFilemanager\Events\FolderIsRenaming;
use UniSharp\LaravelFilemanager\Events\FolderWasRenamed;
use UniSharp\LaravelFilemanager\Events\FileIsRenaming;
use UniSharp\LaravelFilemanager\Events\FileWasRenamed;
use UniSharp\LaravelFilemanager\Events\ImageIsRenaming;
use UniSharp\LaravelFilemanager\Events\ImageWasRenamed;

class RenameController extends LfmController
{
    public function getRename()
    {
        $old_name = $this->helper->input('file');
        $new_name = $this->helper->input('new_name');

        $file = $this->lfm->setName($old_name);

        if (!Storage::disk($this->helper->config('disk'))->exists($file->path('storage'))) {
            abort(404);
        }

        $old_file = $this->lfm->pretty($old_name);

        $is_directory = $file->isDirectory();

        if (empty($new_name)) {
            if ($is_directory) {
                return response()->json(parent::error('folder-name'), 400);
            } else {
                return response()->json(parent::error('file-name'), 400);
            }
        }

        if ($is_directory && config('lfm.alphanumeric_directory')) {
            if (config('lfm.convert_to_alphanumeric')) {
                $new_name = Str::slug($new_name);
            }

            if (preg_match('/[^\w\-_]/i', $new_name)) {
                return parent::error('folder-alnum');
            }
        } elseif (!$is_directory && config('lfm.alphanumeric_filename')) {
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

        $new_path = $this->lfm->setName($new_name)->path('absolute');

        if ($is_directory) {
            event(new FolderIsRenaming($old_file->path(), $new_path));
        } else {
            event(new FileIsRenaming($old_file->path(), $new_path));
            event(new ImageIsRenaming($old_file->path(), $new_path));
        }

        $old_path = $old_file->path();

        if ($old_file->hasThumb()) {
            $this->lfm->setName($old_name)->thumb()
                ->move($this->lfm->setName($new_name)->thumb());
        }

        $this->lfm->setName($old_name)
            ->move($this->lfm->setName($new_name));

        if ($is_directory) {
            event(new FolderWasRenamed($old_path, $new_path));
        } else {
            event(new FileWasRenamed($old_path, $new_path));
            event(new ImageWasRenamed($old_path, $new_path));
        }

        return parent::$success_response;
    }
}
