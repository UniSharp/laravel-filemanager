<?php namespace Unisharp\Laravelfilemanager\controllers;

use Unisharp\Laravelfilemanager\Events\ImageIsRenaming;
use Unisharp\Laravelfilemanager\Events\ImageWasRenamed;
use Unisharp\Laravelfilemanager\Events\FolderIsRenaming;
use Unisharp\Laravelfilemanager\Events\FolderWasRenamed;
use Unisharp\FileApi\FileApi;

/**
 * Class RenameController
 * @package Unisharp\Laravelfilemanager\controllers
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
        $working_dir = parent::getCurrentPath();
        $fa = new FileApi($working_dir);

        if (empty($new_name)) {
            if ($fa->isDirectory($old_name)) {
                return parent::error('folder-name');
            } else {
                return parent::error('file-name');
            }
        }

        if (config('lfm.alphanumeric_directory') && preg_match('/[^\w-]/i', $new_name)) {
            return parent::error('folder-alnum');
        } elseif ($fa->exists($new_name)) {
            return parent::error('rename');
        }

        if (!$fa->isDirectory($old_name)) {
            $extension = $fa->extension($old_name);
            $new_name = str_replace('.' . $extension, '', $new_name) . '.' . $extension;
        }

        $old_file = $working_dir . DIRECTORY_SEPARATOR . $old_name;
        $new_file = $working_dir . DIRECTORY_SEPARATOR . $new_name;

        if ($fa->isDirectory($old_name)) {
            event(new FolderIsRenaming($old_file, $new_file));
        } else {
            event(new ImageIsRenaming($old_file, $new_file));
        }

        $fa->move($old_name, $new_name);

        if ($fa->isDirectory($old_name)) {
            event(new FolderWasRenamed($old_file, $new_file));
        } else {
            event(new ImageWasRenamed($old_file, $new_file));
        }

        return parent::$success_response;
    }
}
