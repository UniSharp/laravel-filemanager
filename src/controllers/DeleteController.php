<?php namespace Unisharp\Laravelfilemanager\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

/**
 * Class CropController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class DeleteController extends LfmController {

    /**
     * Delete image and associated thumbnail
     *
     * @return mixed
     */
    public function getDelete()
    {
        $name_to_delete = Input::get('items');
        $base = Input::get('base');

        $file_path = base_path() . '/' . $this->file_location;

        if ($base !== '/') {
            $file_path = $file_path . $base . '/';
        }

        $file_to_delete = $file_path . $name_to_delete;
        $thumb_to_delete = $file_path . 'thumbs/' . $name_to_delete;

        if (!File::exists($file_to_delete)) {
            return $file_to_delete . ' not found!';
        }

        if (File::isDirectory($file_to_delete)) {
            if (sizeof(File::files($file_to_delete)) != 0) {
                return 'You cannot delete this folder because it is not empty!';
            }

            File::deleteDirectory($file_to_delete);

            return 'OK';
        }

        File::delete($file_to_delete);

        if (Session::get('lfm_type') == 'Images') {
            File::delete($thumb_to_delete);
        }

        return 'OK';
    }
    
}
