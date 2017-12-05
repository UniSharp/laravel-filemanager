<?php

namespace UniSharp\LaravelFilemanager\controllers;

use UniSharp\LaravelFilemanager\Events\ImageIsRenaming;
use UniSharp\LaravelFilemanager\Events\ImageWasRenamed;
use UniSharp\LaravelFilemanager\Events\FolderIsRenaming;
use UniSharp\LaravelFilemanager\Events\FolderWasRenamed;

class RenameController extends LfmController
{
    public function getRename()
    {
        $old_name = $this->helper->translateFromUtf8(request('file'));
        $new_name = $this->helper->translateFromUtf8(trim(request('new_name')));

        $old_file = $this->lfm->get($old_name);

        $is_directory = $old_file->isDirectory();

        if (empty($new_name)) {
            if ($is_directory) {
                return parent::error('folder-name');
            } else {
                return parent::error('file-name');
            }
        }

        if (config('lfm.alphanumeric_directory') && preg_match('/[^\w-]/i', $new_name)) {
            return parent::error('folder-alnum');
            // return parent::error('file-alnum');
        } elseif ($this->lfm->setName($new_name)->exists()) {
            return parent::error('rename');
        }

        if (! $is_directory) {
            $extension = $old_file->extension();
            if ($extension) {
                $new_name = str_replace('.' . $extension, '', $new_name) . '.' . $extension;
            }
        }

        $new_file = $this->lfm->setName($new_name)->path('absolute');

        if ($is_directory) {
            event(new FolderIsRenaming($old_file->path('absolute'), $new_file));
        } else {
            event(new ImageIsRenaming($old_file->path('absolute'), $new_file));
        }

        if ($old_file->isImage()) {
            $this->lfm->setName($old_name)->thumb()
                ->move($this->lfm->setName($new_name)->thumb());
        }

        $this->lfm->setName($old_name)
            ->move($this->lfm->setName($new_name));

        if ($is_directory) {
            event(new FolderWasRenamed($old_file->path('absolute'), $new_file));
        } else {
            event(new ImageWasRenamed($old_file->path('absolute'), $new_file));
        }

        return parent::$success_response;
    }
}
