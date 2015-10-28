<?php namespace Unisharp\Laravelfilemanager\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

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
        $file_to_rename = Input::get('file');
        $dir = Input::get('dir');
        $new_name = Input::get('new_name');

        $file_path = base_path() . '/' . $this->file_location;
        $user_path = $file_path . '/';

        if ($dir !== '/') {
            $user_path = $user_path . $dir . '/';
        }

        $old_file = $user_path . $file_to_rename;

        if (!File::isDirectory($old_file)) {
            $extension = File::extension($old_file);
            $new_name = str_replace($extension, '', $new_name) . '.' . $extension;
        }

        $thumb_path = $user_path . 'thumbs/';

        $new_file = $user_path . $new_name;
        $new_thumb = $thumb_path . $new_name;
        $old_thumb = $thumb_path . $file_to_rename;

        if (File::exists($new_file)) {
            return 'File name already in use!';
        }

        if (File::isDirectory($old_file)) {
            File::move($old_file, $new_file);
            return 'OK';
        }
        
        File::move($old_file, $new_file);

        if (Session::get('lfm_type') == 'Images') {
            File::move($old_thumb, $new_thumb);
        }

        return 'OK';
    }
}
