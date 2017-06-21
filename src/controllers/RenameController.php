<?php

namespace Unisharp\Laravelfilemanager\controllers;

use Unisharp\Laravelfilemanager\Events\ImageIsRenaming;
use Unisharp\Laravelfilemanager\Events\ImageWasRenamed;
use Unisharp\Laravelfilemanager\Events\FolderIsRenaming;
use Unisharp\Laravelfilemanager\Events\FolderWasRenamed;

class RenameController extends LfmController
{
    public function getRename()
    {
        $old_name = parent::translateFromUtf8(request('file'));
        $new_name = parent::translateFromUtf8(trim(request('new_name')));

        $old_file = $this->lfm->path('full', $old_name);

        $is_directory = parent::isDirectory($old_file);

        if (empty($new_name)) {
            if ($is_directory) {
                return parent::error('folder-name');
            } else {
                return parent::error('file-name');
            }
        }

        if (config('lfm.alphanumeric_directory') && preg_match('/[^\w-]/i', $new_name)) {
            return parent::error('folder-alnum');
        } elseif ($this->lfm->exists($new_name)) {
            return parent::error('rename');
        }

        if (! $is_directory) {
            $extension = \File::extension($old_file);
            if ($extension) {
                $new_name = str_replace('.' . $extension, '', $new_name) . '.' . $extension;
            }
        }

        $new_file = $this->lfm->path('full', $new_name);

        if ($is_directory) {
            event(new FolderIsRenaming($old_file, $new_file));
        } else {
            event(new ImageIsRenaming($old_file, $new_file));
        }

        if (parent::fileIsImage($old_file)) {
            $this->move(
                $this->lfm->thumb()->path('full', $old_name),
                $this->lfm->thumb()->path('full', $new_name)
            );
        }

        $this->move($old_file, $new_file);

        if ($is_directory) {
            event(new FolderWasRenamed($old_file, $new_file));
        } else {
            event(new ImageWasRenamed($old_file, $new_file));
        }

        return parent::$success_response;
    }
}
