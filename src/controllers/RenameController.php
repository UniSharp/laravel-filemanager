<?php namespace Unisharp\Laravelfilemanager\controllers;

use Illuminate\Support\Facades\Event;
use Unisharp\Laravelfilemanager\controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use Lang;
use Unisharp\Laravelfilemanager\Events\ImageIsRenaming;
use Unisharp\Laravelfilemanager\Events\ImageWasRenamed;
use Unisharp\Laravelfilemanager\Events\FolderIsRenaming;
use Unisharp\Laravelfilemanager\Events\FolderWasRenamed;

/**
 * Class RenameController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class RenameController extends LfmController {

    /**
     * @return string
     */
    public function getRename()
    {
        $old_name = Input::get('file');
        $new_name = trim(Input::get('new_name'));

        $file_path  = parent::getPath('directory');
        $thumb_path = parent::getPath('thumb');

        $old_file = $file_path . $old_name;

        if (!File::isDirectory($old_file)) {
            $extension = File::extension($old_file);
            $new_name = str_replace('.' . $extension, '', $new_name) . '.' . $extension;
        }

        $new_file = $file_path . $new_name;

        if (File::isDirectory($old_file)) {
            Event::fire(new FolderIsRenaming($old_file, $new_file));
        } else {
            Event::fire(new ImageIsRenaming($old_file, $new_file));
        }

        if (Config::get('lfm.alphanumeric_directory') && preg_match('/[^\w-]/i', $new_name)) {
            return Lang::get('laravel-filemanager::lfm.error-folder-alnum');
        } elseif (File::exists($new_file)) {
            return Lang::get('laravel-filemanager::lfm.error-rename');
        }

        if (File::isDirectory($old_file)) {
            File::move($old_file, $new_file);
            Event::fire(new FolderWasRenamed($old_file, $new_file));
            return 'OK';
        }

        File::move($old_file, $new_file);

        if ('Images' === $this->file_type) {
            File::move($thumb_path . $old_name, $thumb_path . $new_name);
        }

        Event::fire(new ImageWasRenamed($old_file, $new_file));

        return 'OK';
    }
}
